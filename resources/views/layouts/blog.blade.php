<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Blog')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('blog.index') }}" class="text-xl font-bold text-gray-900">
                        📝 {{ config('app.name', 'Blog') }}
                    </a>
                </div>
                <nav>
                    <a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2">Início</a>
                    <a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Admin</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-gray-600">
                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
            </p>
        </div>
    </footer>
</body>
</html> 