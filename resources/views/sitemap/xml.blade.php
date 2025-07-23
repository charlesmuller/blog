@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc>{{ route('blog.index') }}</loc>
        <lastmod>{{ now()->toW3cString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Posts -->
    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.post', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    
    <!-- Categories -->
    @foreach($categories as $category)
    <url>
        <loc>{{ route('blog.category', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    
    <!-- Tags -->
    @foreach($tags as $tag)
    <url>
        <loc>{{ route('blog.tag', $tag->slug) }}</loc>
        <lastmod>{{ $tag->updated_at->toW3cString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    @endforeach
</urlset> 