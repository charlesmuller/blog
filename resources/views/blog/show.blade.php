@extends('layouts.blog')

@section('title', $post->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex text-sm text-gray-500 space-x-2">
            <li><a href="{{ route('blog.index') }}" class="hover:text-blue-600">Blog</a></li>
            <li>→</li>
            @if($post->category)
                <li><a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-blue-600">{{ $post->category->name }}</a></li>
                <li>→</li>
            @endif
            <li class="text-gray-900">{{ $post->title }}</li>
        </ol>
    </nav>

    <!-- Post Header -->
    <header class="mb-8">
        <div class="mb-4">
            <div class="flex items-center text-sm text-gray-500 space-x-4">
                <time datetime="{{ $post->published_at->toISOString() }}">
                    {{ $post->published_at->format('d \d\e F \d\e Y') }}
                </time>
                @if($post->category)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        {{ $post->category->name }}
                    </span>
                @endif
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ $post->views_count ?? 0 }} visualizações
                </span>
            </div>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

        @if($post->excerpt)
            <p class="text-xl text-gray-600 leading-relaxed mb-8">{{ $post->excerpt }}</p>
        @endif

        @if($post->featured_image)
            <div class="mb-8">
                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full rounded-lg shadow-lg">
            </div>
        @endif
    </header>

    <!-- Post Content -->
    <div class="prose prose-lg max-w-none mb-12">
        {!! $post->content !!}
    </div>

    <!-- Post Footer -->
    <footer class="border-t pt-8">
        <!-- Tags -->
        @if($post->tags && $post->tags->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Tags:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" 
                           class="inline-block bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Author -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Sobre o Autor</h3>
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ substr($post->user->name, 0, 1) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $post->user->name }}</p>
                    <p class="text-gray-600 text-sm">Autor do blog</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Related Posts -->
    @if(isset($relatedPosts) && $relatedPosts->count() > 0)
        <section class="border-t pt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Posts Relacionados</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedPosts as $relatedPost)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        @if($relatedPost->featured_image)
                            <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" 
                                 alt="{{ $relatedPost->title }}" 
                                 class="w-full h-32 object-cover">
                        @endif
                        
                        <div class="p-4">
                            <div class="text-sm text-gray-500 mb-2">
                                {{ $relatedPost->published_at->format('d/m/Y') }}
                            </div>
                            
                            <h3 class="font-semibold text-gray-900 mb-2">
                                <a href="{{ route('blog.post', $relatedPost->slug) }}" class="hover:text-blue-600 transition-colors">
                                    {{ Str::limit($relatedPost->title, 60) }}
                                </a>
                            </h3>
                            
                            @if($relatedPost->excerpt)
                                <p class="text-gray-600 text-sm">{{ Str::limit($relatedPost->excerpt, 80) }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Navigation -->
    <nav class="mt-12 pt-8 border-t">
        <div class="flex justify-between items-center">
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                ← Voltar para o Blog
            </a>
            
            <div class="flex space-x-4">
                <button onclick="window.history.back()" 
                        class="text-gray-600 hover:text-gray-800 transition-colors">
                    ← Voltar
                </button>
                <button onclick="window.print()" 
                        class="text-gray-600 hover:text-gray-800 transition-colors">
                    🖨️ Imprimir
                </button>
            </div>
        </div>
    </nav>
</div>

<style>
.prose {
    color: #374151;
    line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #111827;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose p {
    margin-bottom: 1.5rem;
}

.prose ul, .prose ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose blockquote {
    border-left: 4px solid #3B82F6;
    padding-left: 1rem;
    margin: 2rem 0;
    font-style: italic;
    background-color: #F8FAFC;
    padding: 1rem;
    border-radius: 0.5rem;
}

.prose code {
    background-color: #F3F4F6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.prose pre {
    background-color: #1F2937;
    color: #F9FAFB;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.prose img {
    border-radius: 0.5rem;
    margin: 2rem 0;
}

.prose a {
    color: #3B82F6;
    text-decoration: underline;
}

.prose a:hover {
    color: #1E40AF;
}
</style>
@endsection 