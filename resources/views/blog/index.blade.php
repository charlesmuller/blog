@extends('layouts.blog')

@section('title', 'Início')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Blog</h1>
        <p class="text-xl text-gray-600">Histórias, reflexões e experiências da vida</p>
    </div>

    @if($posts && $posts->count() > 0)
        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-48 object-cover">
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <span>{{ $post->published_at->format('d/m/Y') }}</span>
                            @if($post->category)
                                <span class="mx-2">•</span>
                                <span class="text-blue-600">{{ $post->category->name }}</span>
                            @endif
                        </div>
                        
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">
                            <a href="{{ route('blog.post', $post->slug) }}" class="hover:text-blue-600 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h2>
                        
                        @if($post->excerpt)
                            <p class="text-gray-600 mb-4">{{ Str::limit($post->excerpt, 120) }}</p>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <a href="{{ route('blog.post', $post->slug) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                Ler mais →
                            </a>
                            
                            <div class="flex items-center text-gray-500 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $post->views_count ?? 0 }}
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-6xl mb-4">📝</div>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Nenhum post encontrado</h2>
            <p class="text-gray-600">Em breve teremos conteúdo novo por aqui!</p>
        </div>
    @endif

    <!-- Sidebar -->
    <aside class="mt-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Categories -->
            @if($categories && $categories->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Categorias</h3>
                    <div class="space-y-2">
                        @foreach($categories as $category)
                            <a href="{{ route('blog.category', $category->slug) }}" 
                               class="flex justify-between items-center text-gray-600 hover:text-blue-600 transition-colors">
                                <span>{{ $category->name }}</span>
                                <span class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $category->posts_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($tags && $tags->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" 
                               class="inline-block bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                                {{ $tag->name }}
                                @if($tag->posts_count > 0)
                                    ({{ $tag->posts_count }})
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </aside>
</div>
@endsection 