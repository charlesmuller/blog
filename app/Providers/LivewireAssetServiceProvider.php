<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LivewireAssetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Redirect vendor/livewire routes to livewire routes
        Route::get('/vendor/livewire/{path}', function ($path) {
            return redirect("/livewire/{$path}", 301);
        })->where('path', '.*');
    }
} 