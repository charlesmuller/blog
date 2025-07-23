<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap
     */
    public function index(): Response
    {
        $posts = Post::published()
            ->select(['slug', 'updated_at', 'published_at'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = Category::select(['slug', 'updated_at'])->get();
        $tags = Tag::select(['slug', 'updated_at'])->get();

        $sitemap = view('sitemap.xml', compact('posts', 'categories', 'tags'));

        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache por 1 hora
    }

    /**
     * Generate robots.txt
     */
    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /health\n\n";
        $content .= "Sitemap: " . route('sitemap') . "\n";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=86400'); // Cache por 24 horas
    }
}
