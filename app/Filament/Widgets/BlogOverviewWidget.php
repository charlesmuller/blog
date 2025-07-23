<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total de Posts', Post::count())
                ->description('Posts no blog')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Posts Publicados', Post::where('status', 'published')->count())
                ->description('Posts públicos')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make('Posts em Rascunho', Post::where('status', 'draft')->count())
                ->description('Posts não publicados')
                ->descriptionIcon('heroicon-m-pencil')
                ->color('warning'),

            Stat::make('Categorias', Category::count())
                ->description('Total de categorias')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Tags', Tag::count())
                ->description('Total de tags')
                ->descriptionIcon('heroicon-m-tag')
                ->color('gray'),

            Stat::make('Visualizações', Post::sum('views_count'))
                ->description('Total de visualizações')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
