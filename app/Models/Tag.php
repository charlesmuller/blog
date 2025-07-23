<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    // Auto-generate slug from name
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // Relacionamentos
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()->published();
    }

    // Accessor para URL
    public function getUrlAttribute(): string
    {
        return route('blog.tag', $this->slug);
    }
}
