<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;

Route::get('/', function () {
    return view('welcome');
});



// Rotas do Blog Público
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/post/{slug}', [BlogController::class, 'show'])->name('post');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('tag');
});

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Rota de Health Check
Route::get('/health', function () {
    $checks = [
        'database' => false,
        'redis' => false,
        'storage' => false,
    ];

    try {
        // Check database
        DB::connection()->getPdo();
        $checks['database'] = true;
    } catch (Exception $e) {
        //
    }

    try {
        // Check Redis
        Redis::ping();
        $checks['redis'] = true;
    } catch (Exception $e) {
        //
    }

    try {
        // Check storage
        Storage::disk('public')->exists('');
        $checks['storage'] = true;
    } catch (Exception $e) {
        //
    }

    $allHealthy = collect($checks)->every(fn($check) => $check === true);

    return response()->json([
        'status' => $allHealthy ? 'healthy' : 'unhealthy',
        'checks' => $checks,
        'timestamp' => now(),
    ], $allHealthy ? 200 : 503);
});

// Livewire asset compatibility routes
Route::get('/vendor/livewire/{path}', function ($path) {
    return redirect("/livewire/{$path}", 301);
})->where('path', '.*');
