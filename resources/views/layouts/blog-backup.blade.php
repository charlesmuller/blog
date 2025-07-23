<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', 'Blog') - {{ config('app.name', 'Filament Blog') }}</title>
    <meta name="title" content="@yield('title', 'Blog') - {{ config('app.name', 'Filament Blog') }}">
    <meta name="description" content="@yield('description', 'Blog pessoal com histórias, reflexões e experiências da vida. Descubra narrativas que tocam o coração e inspiram a alma.')">
    <meta name="keywords" content="@yield('keywords', 'blog, histórias, reflexões, vida, experiências, narrativas, literatura')">
    <meta name="author" content="{{ config('app.name', 'Filament Blog') }}">
    <meta name="robots" content="index, follow">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Blog') - {{ config('app.name', 'Filament Blog') }}">
    <meta property="og:description" content="@yield('description', 'Blog pessoal com histórias, reflexões e experiências da vida.')">
    <meta property="og:image" content="@yield('og_image', asset('images/blog-og.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ config('app.name', 'Filament Blog') }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'Blog') - {{ config('app.name', 'Filament Blog') }}">
    <meta property="twitter:description" content="@yield('description', 'Blog pessoal com histórias, reflexões e experiências da vida.')">
    <meta property="twitter:image" content="@yield('og_image', asset('images/blog-og.jpg'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#3B82F6">
    <meta name="msapplication-TileColor" content="#3B82F6">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS CDN (para desenvolvimento rápido) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Structured Data -->
    @stack('structured-data')
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    
    <!-- Additional SEO Scripts -->
    @stack('head-scripts')
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Skip to content (accessibility) -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-md z-50">
        Pular para o conteúdo
    </a>

    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <nav class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" role="navigation" aria-label="Navegação principal">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('blog.index') }}" 
                       class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors"
                       aria-label="Página inicial do blog">
                        📝 {{ config('app.name', 'Blog') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('blog.index') }}" 
                       class="text-gray-600 hover:text-gray-900 transition-colors"
                       @if(request()->routeIs('blog.index')) aria-current="page" @endif>
                        Início
                    </a>
                    <a href="/admin" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                       target="_blank"
                       rel="noopener noreferrer">
                        Admin
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" 
                            class="text-gray-600 hover:text-gray-900"
                            aria-label="Menu de navegação móvel"
                            aria-expanded="false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb Schema -->
    @if(isset($breadcrumbs))
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                @foreach($breadcrumbs as $index => $breadcrumb)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 }},
                    "name": "{{ $breadcrumb['name'] }}",
                    "item": "{{ $breadcrumb['url'] }}"
                }@if(!$loop->last),@endif
                @endforeach
            ]
        }
        </script>
    @endif

    <!-- Main Content -->
    <main id="main-content" class="min-h-screen" role="main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16" role="contentinfo">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sobre o Blog</h3>
                    <p class="text-gray-600">
                        Um espaço para compartilhar histórias, reflexões e experiências da vida.
                        Descubra narrativas que tocam o coração e inspiram a alma.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Links Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Todos os Posts</a></li>
                        <li><a href="/admin" class="text-gray-600 hover:text-gray-900 transition-colors" target="_blank" rel="noopener noreferrer">Painel Admin</a></li>
                        <li><a href="/health" class="text-gray-600 hover:text-gray-900 transition-colors">Status do Sistema</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tecnologia</h3>
                    <p class="text-gray-600 mb-4">
                        Criado com ❤️ usando:
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Laravel {{ app()->version() }}</li>
                        <li>• Filament PHP</li>
                        <li>• Tailwind CSS</li>
                        <li>• MySQL</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-8 pt-8 text-center">
                <p class="text-gray-600">
                    © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Última atualização: {{ date('d/m/Y') }}
                </p>
            </div>
        </div>
    </footer>

    <!-- Website Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ config('app.name', 'Filament Blog') }}",
        "url": "{{ config('app.url') }}",
        "description": "Blog pessoal com histórias, reflexões e experiências da vida.",
        "inLanguage": "pt-BR",
        "author": {
            "@type": "Person",
            "name": "{{ config('app.name', 'Filament Blog') }}"
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ route('blog.index') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html> 