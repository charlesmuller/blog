<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Timeline de posts do blog (página inicial)
     */
    public function timeline(Request $request): View
    {
        $posts = Post::published()
            ->with(['category', 'user', 'tags'])
            ->latest('published_at')
            ->paginate(5); // Poucos posts por página já que mostra conteúdo completo

        $categories = Category::withCount(['posts' => function ($query) {
            $query->published();
        }])->orderBy('sort_order')->get();

        $tags = Tag::withCount(['posts' => function ($query) {
            $query->published();
        }])->orderBy('name')->get();

        return view('blog.timeline', compact('posts', 'categories', 'tags'));
    }

    /**
     * Lista de posts em grid (cards)
     */
    public function grid(Request $request): View
    {
        $posts = Post::published()
            ->with(['category', 'user', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        $categories = Category::withCount(['posts' => function ($query) {
            $query->published();
        }])->orderBy('sort_order')->get();

        $tags = Tag::withCount(['posts' => function ($query) {
            $query->published();
        }])->orderBy('name')->get();

        return view('blog.index', compact('posts', 'categories', 'tags'));
    }

    /**
     * Exibir post específico
     */
    public function show(string $slug): View
    {
        $post = Post::published()
            ->with(['category', 'user', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Incrementar contador de visualizações
        $post->increment('views_count');

        // Posts relacionados (mesma categoria)
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Posts por categoria
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->with(['category', 'user', 'tags'])
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        return view('blog.category', compact('category', 'posts'));
    }

    /**
     * Posts por tag
     */
    public function tag(string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->with(['category', 'user', 'tags'])
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tag_id', $tag->id);
            })
            ->latest('published_at')
            ->paginate(12);

        return view('blog.tag', compact('tag', 'posts'));
    }
}
