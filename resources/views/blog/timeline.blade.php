@extends('layouts.blog')

@section('title', 'Charles Müller - Blog')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Blog</h1>
        <p class="text-xl text-gray-600 mb-8">Histórias, reflexões e experiências da vida</p>
        
        <!-- View Toggle -->
        <div class="flex justify-center space-x-4 mb-8">
            <span class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                </svg>
                Timeline
            </span>
            <a href="{{ route('blog.grid') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Cards
            </a>
        </div>
    </div>

    @if($posts && $posts->count() > 0)
        <!-- Timeline Posts -->
        <div class="space-y-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <!-- Post Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <time class="font-medium">{{ $post->published_at->format('d/m/Y') }}</time>
                            @if($post->category)
                                <span class="mx-2">•</span>
                                <a href="{{ route('blog.category', $post->category->slug) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $post->category->name }}
                                </a>
                            @endif
                        </div>
                        
                        <div class="flex items-center text-gray-500 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $post->views_count ?? 0 }}
                        </div>
                    </div>

                    <!-- Post Title -->
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <a href="{{ route('blog.post', $post->slug) }}" 
                           class="hover:text-blue-600 transition-colors">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <!-- Post Content -->
                    <div class="prose prose-lg max-w-none mb-6">
                        @if($post->content)
                            {!! $post->content !!}
                        @elseif($post->excerpt)
                            <p class="text-gray-600 leading-relaxed">
                                {{ $post->excerpt }}
                            </p>
                        @endif
                    </div>

                    <!-- Post Tags -->
                    @if($post->tags && $post->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.tag', $tag->slug) }}" 
                                   class="inline-block bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-2 py-1 rounded text-xs transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Post Actions -->
                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <a href="{{ route('blog.post', $post->slug) }}" 
                               class="hover:text-blue-600 transition-colors">
                                Link permanente
                            </a>
                            @if($post->category)
                                <span>•</span>
                                <a href="{{ route('blog.category', $post->category->slug) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    Mais em {{ $post->category->name }}
                                </a>
                            @endif
                        </div>
                        
                        <time class="text-sm text-gray-400" datetime="{{ $post->published_at->toISOString() }}">
                            {{ $post->published_at->diffForHumans() }}
                        </time>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
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
