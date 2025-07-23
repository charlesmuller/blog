@extends('layouts.blog')

@section('title', 'Tag: ' . $tag->name)

@section('description', 'Posts com a tag ' . $tag->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('blog.index') }}" class="hover:text-blue-600">Início</a></li>
            <li class="flex items-center">
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900">#{{ $tag->name }}</span>
            </li>
        </ol>
    </nav>

    <!-- Tag Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full mb-6">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
        </div>
        
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            #{{ $tag->name }}
        </h1>
        
        <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-6">
            Explore todos os posts relacionados com esta tag
        </p>
        
        <div class="mt-6">
            <span class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 px-4 py-2 rounded-full text-sm font-medium">
                {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }} encontrado{{ $posts->total() == 1 ? '' : 's' }}
            </span>
        </div>
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 group">
                    @if($post->featured_image)
                        <div class="relative overflow-hidden">
                            <img src="{{ Storage::url($post->featured_image) }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-3 right-3">
                                <span class="bg-white/90 backdrop-blur-sm text-gray-700 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $post->category->name }}
                                </span>
                            </div>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-{{ $post->category->color ?? 'blue' }}-100 text-{{ $post->category->color ?? 'blue' }}-800 text-xs font-medium px-2 py-1 rounded-full">
                                {{ $post->category->name }}
                            </span>
                            <span class="text-gray-500 text-sm">
                                {{ $post->published_at->format('d/m/Y') }}
                            </span>
                        </div>
                        
                        <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-purple-600 transition-colors">
                            <a href="{{ route('blog.post', $post->slug) }}">
                                {{ Str::limit($post->title, 60) }}
                            </a>
                        </h2>
                        
                        @if($post->excerpt)
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                {{ Str::limit($post->excerpt, 120) }}
                            </p>
                        @endif
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $post->views_count }}
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $post->user->name }}
                            </div>
                        </div>
                        
                        @if($post->tags->count() > 0)
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($post->tags->take(4) as $postTag)
                                        <span class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 text-xs px-2 py-1 rounded-full {{ $postTag->id == $tag->id ? 'ring-2 ring-purple-300' : '' }}">
                                            #{{ $postTag->name }}
                                        </span>
                                    @endforeach
                                    @if($post->tags->count() > 4)
                                        <span class="text-gray-500 text-xs">
                                            +{{ $post->tags->count() - 4 }}
                                        </span>
                                    @endif
                                </div>
                                
                                <a href="{{ route('blog.post', $post->slug) }}" 
                                   class="inline-flex items-center mt-3 text-purple-600 hover:text-purple-800 font-medium text-sm group-hover:translate-x-1 transition-transform">
                                    Ler artigo completo →
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $posts->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum post encontrado</h3>
            <p class="text-gray-600 mb-8">
                Ainda não há posts publicados com a tag "#{{ $tag->name }}".
            </p>
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-lg font-medium transition-all">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Explorar todas as publicações
            </a>
        </div>
    @endif

    <!-- Related Tags -->
    @if($posts->count() > 0)
        @php
            $currentTagId = $tag->id;
            $relatedTags = collect($posts->items())
                ->flatMap(fn($post) => $post->tags)
                ->unique('id')
                ->filter(fn($relatedTag) => $relatedTag->id !== $currentTagId)
                ->take(8);
        @endphp
        
        @if($relatedTags->count() > 0)
            <div class="mt-16 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Tags Relacionadas</h3>
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach($relatedTags as $relatedTag)
                        <a href="{{ route('blog.tag', $relatedTag->slug) }}" 
                           class="bg-gray-100 hover:bg-gradient-to-r hover:from-purple-100 hover:to-pink-100 text-gray-700 hover:text-purple-700 px-3 py-2 rounded-full text-sm transition-all">
                            #{{ $relatedTag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- Back to Blog -->
    <div class="mt-12 pt-8 border-t border-gray-200">
        <a href="{{ route('blog.index') }}" 
           class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Voltar ao Blog
        </a>
    </div>
</div>
@endsection 