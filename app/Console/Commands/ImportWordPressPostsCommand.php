<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportWordPressPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'blog:import-wordpress {file=docs/posts_blog.json} {--user-id=1 : ID do usuário que será o autor dos posts}';

    /**
     * The console command description.
     */
    protected $description = 'Importa posts do WordPress a partir de um arquivo JSON';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $userId = $this->option('user-id');

        // Verificar se o arquivo existe
        if (!file_exists($filePath)) {
            $this->error("Arquivo não encontrado: {$filePath}");
            return Command::FAILURE;
        }

        // Verificar se o usuário existe
        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuário com ID {$userId} não encontrado.");
            return Command::FAILURE;
        }

        $this->info("🚀 Iniciando importação dos posts do WordPress...");
        $this->info("📁 Arquivo: {$filePath}");
        $this->info("👤 Autor: {$user->name} (ID: {$userId})");

        // Ler e decodificar o JSON
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('❌ Erro ao decodificar JSON: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        $this->info("📊 Total de posts encontrados: " . count($data));

        // Processar os posts
        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        DB::beginTransaction();

        try {
            foreach ($data as $index => $postData) {
                $this->line("📝 Processando post " . ($index + 1) . "/" . count($data) . "...");

                // Verificar se o post já foi importado
                if (Post::where('wordpress_id', $postData['id'] ?? null)->exists()) {
                    $title = $postData['title']['rendered'] ?? 'Post sem título';
                    $this->line("⏭️  Post '{$title}' já existe. Pulando...");
                    $skippedCount++;
                    continue;
                }

                try {
                    // Processar categoria (primeiro da lista ou padrão)
                    $category = $this->processCategory($postData);

                    // Criar o post
                    $post = $this->createPost($postData, $userId, $category);

                    $importedCount++;
                    $this->info("✅ Post '{$post->title}' importado com sucesso!");

                } catch (\Exception $e) {
                    $errorCount++;
                    $title = $postData['title']['rendered'] ?? 'Post sem título';
                    $this->error("❌ Erro ao importar '{$title}': " . $e->getMessage());
                }
            }

            DB::commit();

            $this->newLine();
            $this->info("🎉 Importação concluída!");
            $this->info("✅ Posts importados: {$importedCount}");
            $this->info("⏭️  Posts pulados: {$skippedCount}");
            if ($errorCount > 0) {
                $this->warn("❌ Posts com erro: {$errorCount}");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("💥 Erro durante a importação: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Processar categoria do post (WordPress API format)
     */
    private function processCategory(array $postData): ?Category
    {
        // WordPress retorna array de IDs de categoria
        $categoryIds = $postData['categories'] ?? [];
        
        if (empty($categoryIds)) {
            return $this->getDefaultCategory();
        }

        // Por simplicidade, vamos usar um mapeamento básico ou categoria padrão
        // Em um cenário real, você poderia fazer uma API call para obter nomes das categorias
        $categoryMappings = [
            1377 => 'Vida',       // ID encontrado no JSON
            1 => 'Geral',         // Categoria padrão do WordPress
        ];

        $firstCategoryId = $categoryIds[0];
        $categoryName = $categoryMappings[$firstCategoryId] ?? 'Importados';

        return Category::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            [
                'name' => $categoryName,
                'description' => "Categoria importada do WordPress (ID: {$firstCategoryId})",
                'color' => '#6366f1',
                'sort_order' => 0,
            ]
        );
    }

    /**
     * Obter categoria padrão
     */
    private function getDefaultCategory(): Category
    {
        return Category::firstOrCreate(
            ['slug' => 'geral'],
            [
                'name' => 'Geral',
                'description' => 'Categoria padrão para posts importados',
                'color' => '#6366f1',
                'sort_order' => 0,
            ]
        );
    }

    /**
     * Criar o post (WordPress API format)
     */
    private function createPost(array $postData, int $userId, ?Category $category): Post
    {
        // Extrair dados do formato WordPress API
        $title = $postData['title']['rendered'] ?? 'Post sem título';
        $content = $postData['content']['rendered'] ?? '';
        $excerpt = $postData['excerpt']['rendered'] ?? '';
        $slug = $postData['slug'] ?? Str::slug($title);
        
        // Processar data de publicação
        $publishedAt = null;
        if (!empty($postData['date'])) {
            try {
                $publishedAt = Carbon::parse($postData['date']);
            } catch (\Exception $e) {
                $publishedAt = now();
            }
        }

        // Processar status
        $status = 'published';
        if (isset($postData['status'])) {
            $wpStatus = strtolower($postData['status']);
            $status = match($wpStatus) {
                'publish' => 'published',
                'draft' => 'draft',
                'private' => 'archived',
                default => 'published'
            };
        }

        // Limpar excerpt (remover HTML tags do WordPress)
        if (!empty($excerpt)) {
            $excerpt = strip_tags($excerpt);
            $excerpt = str_replace(['&hellip;', '&nbsp;'], ['...', ' '], $excerpt);
            $excerpt = trim($excerpt);
        }

        // Se não tem excerpt, criar um do conteúdo
        if (empty($excerpt) && !empty($content)) {
            $cleanContent = strip_tags($content);
            $excerpt = Str::limit($cleanContent, 150);
        }

        return Post::create([
            'title' => $title,
            'slug' => $this->generateUniqueSlug($slug),
            'excerpt' => $excerpt,
            'content' => $content,
            'featured_image' => $postData['jetpack_featured_media_url'] ?? null,
            'status' => $status,
            'published_at' => $publishedAt ?? now(),
            'category_id' => $category?->id,
            'user_id' => $userId,
            'meta' => [
                'wordpress_data' => $postData,
                'imported_at' => now(),
                'original_link' => $postData['link'] ?? null,
            ],
            'wordpress_id' => (string) $postData['id'],
            'views_count' => 0,
        ]);
    }

    /**
     * Gerar slug único
     */
    private function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
