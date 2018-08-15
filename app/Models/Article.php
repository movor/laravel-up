<?php

namespace App\Models;

use App\Models\CustomCasts\ArticleFeaturedImageCast;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Movor\LaravelCustomCasts\CustomCastableTrait;
use Movor\LaravelDbRedirector\Models\RedirectRule;
use Parsedown;

class Article extends Model
{
    use CrudTrait, CustomCastableTrait;

    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'body',
        'slug',
        'featured',
        'featured_image',
        'commentable',
        'published_at'
    ];

    protected $casts = [
        'featured_image' => ArticleFeaturedImageCast::class,
        'published_at' => 'datetime',
        'featured' => 'boolean',
        'commentable' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        // Create 301 redirect when slug changes
        static::updated(function (Article $article) {
            if ($article->isDirty('slug')) {
                RedirectRule::create([
                    'origin' => 'article/' . $article->getOriginal('slug'),
                    'destination' => 'article/' . $article->slug
                ]);
            }
        });

        // Remove redirects when post is deleted
        static::deleted(function (Article $article) {
            try {
                RedirectRule::deleteChainedRecursively('article/' . $article->slug);
            } catch (\Exception $e) {
            }
        });
    }

    /**
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->orderBy('primary', 'desc')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsPublishedAttribute()
    {
        return (bool) $this->published_at;
    }

    public function getFeaturedImageRawAttribute()
    {
        return $this->getOriginal('featured_image');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = title_case($value);
    }

    public function getBodyHtmlAttribute()
    {
        $rendered = (new Parsedown)->text($this->body);

        $replace = [
            // Customizations (Bootstrap classes)
            '<table>' => '<table class="table">',
            // Resolve curly braces ("{{") Vue rendering
            '<code' => '<code v-pre',
            // Links always in new tab
            '<a href="' => '<a target="_blank" href="',
        ];

        return str_replace(array_keys($replace), $replace, $rendered);
    }

    public function getPrimaryTag()
    {
        $tag = $this->tags()->where('primary', true)->first();

        return $tag ?: $this->tags()->first();
    }

    public function scopeFeatured($query, $featured = true)
    {
        return $query->where('featured', $featured);
    }

    public function scopePublished($query, $published = true)
    {
        return $published
            ? $query->whereNotNull('published_at')
            : $query->whereNull('published_at');
    }

    public function getUrl()
    {
        return url('article/' . $this->slug);
    }
}