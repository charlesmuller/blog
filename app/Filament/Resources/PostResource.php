<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Posts';

    protected static ?string $modelLabel = 'Post';

    protected static ?string $pluralModelLabel = 'Posts';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Título e Slug
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                TextInput::make('slug')
                    ->label('URL Amigável')
                    ->required()
                    ->maxLength(255)
                    ->unique(Post::class, 'slug', ignoreRecord: true),

                // Resumo
                Textarea::make('excerpt')
                    ->label('Resumo')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),

                // Conteúdo
                RichEditor::make('content')
                    ->label('Conteúdo')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ]),

                // Imagem destacada
                FileUpload::make('featured_image')
                    ->label('Imagem Destacada')
                    ->image()
                    ->directory('posts')
                    ->visibility('public'),

                // Status
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'archived' => 'Arquivado',
                    ])
                    ->default('draft')
                    ->required(),

                // Data de publicação
                DateTimePicker::make('published_at')
                    ->label('Data de Publicação')
                    ->default(now())
                    ->required(),

                // Categoria
                Select::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('slug')
                            ->label('Slug'),
                        Textarea::make('description')
                            ->label('Descrição'),
                    ])
                    ->searchable()
                    ->preload(),

                // Tags
                Select::make('tags')
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('slug')
                            ->label('Slug'),
                    ])
                    ->searchable()
                    ->preload(),

                // Usuário (autor)
                Select::make('user_id')
                    ->label('Autor')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Imagem
                ImageColumn::make('featured_image')
                    ->label('Imagem')
                    ->size(50)
                    ->circular(),

                // Título
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                // Categoria
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                // Status
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                        'danger' => 'archived',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-eye' => 'published',
                        'heroicon-o-archive-box' => 'archived',
                    ]),

                // Autor
                TextColumn::make('user.name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable(),

                // Data de publicação
                TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                // Visualizações
                TextColumn::make('views_count')
                    ->label('Visualizações')
                    ->numeric()
                    ->sortable(),

                // Data de criação
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'archived' => 'Arquivado',
                    ]),

                SelectFilter::make('category')
                    ->label('Categoria')
                    ->relationship('category', 'name'),

                SelectFilter::make('user')
                    ->label('Autor')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}
