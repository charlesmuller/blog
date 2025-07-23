@extends('layouts.blog')

@section('title', 'Categoria: ' . $category->name)

@section('description', $category->description ?? 'Posts da categoria ' . $category->name)

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
                <span class="text-gray-900">{{ $category->name }}</span>
            </li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-{{ $category->color ?? 'blue' }}-100 rounded-full mb-6">
            <span class="bg-{{ $category->color ?? 'blue' }}-500 text-white px-4 py-2 rounded-full font-medium">
                {{ substr($category->name, 0, 1) }}
            </span>
        </div>
        
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            {{ $category->name }}
        </h1>
        
        @if($category->description)
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                {{ $category->description }}
            </p>
        @endif
        
        <div class="mt-6">
            <span class="text-gray-500">
                {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }} nesta categoria
            </span>
        </div>
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                    @if($post->featured_image)
                        <div class="relative overflow-hidden">
                            <img src="{{ Storage::url($post->featured_image) }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-{{ $category->color ?? 'blue' }}-100 text-{{ $category->color ?? 'blue' }}-800 text-xs font-medium px-2 py-1 rounded-full">
                                {{ $post->category->name }}
                            </span>
                            <span class="text-gray-500 text-sm">
                                {{ $post->published_at->format('d/m/Y') }}
                            </span>
                        </div>
                        
                        <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                            <a href="{{ route('blog.post', $post->slug) }}">
                                {{ Str::limit($post->title, 60) }}
                            </a>
                        </h2>
                        
                        @if($post->excerpt)
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                {{ Str::limit($post->excerpt, 120) }}
                            </p>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $post->views_count }}
                            </div>
                            
                            <a href="{{ route('blog.post', $post->slug) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                Ler mais →
                            </a>
                        </div>
                        
                        @if($post->tags->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($post->tags->take(3) as $tag)
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                    @if($post->tags->count() > 3)
                                        <span class="text-gray-500 text-xs">
                                            +{{ $post->tags->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
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
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum post encontrado</h3>
            <p class="text-gray-600 mb-8">
                Ainda não há posts publicados na categoria "{{ $category->name }}".
            </p>
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Voltar ao Blog
            </a>
        </div>
    @endif

    <!-- Back to Blog -->
    <div class="mt-12 pt-8 border-t border-gray-200">
        <a href="{{ route('blog.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Voltar ao Blog
        </a>
    </div>
</div>
@endsection 