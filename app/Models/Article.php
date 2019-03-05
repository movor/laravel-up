<?php

namespace App\Models;

use App\CustomCasts\ArticleFeaturedImageCast;
use App\Events\ArticlePublishedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Parsedown;
use Vkovic\LaravelCustomCasts\HasCustomCasts;

class Article extends Model
{
    use HasCustomCasts, DisplayableTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'featured_image' => ArticleFeaturedImageCast::class,
        'published_at' => 'datetime',
        'featured' => 'boolean',
        'commentable' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'published' => ArticlePublishedEvent::class
    ];

    public static function boot()
    {
        parent::boot();

        static::saved(function (Article $article) {
            // Fire custom event "published", when article is published
            if ($article->isDirty('published_at')) {
                $article->fireModelEvent('published');
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

    public function getPrimaryTag()
    {
        return $this->tags()->wherePivot('primary', true)->first();
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

    public static function getBaseUri()
    {
        return 'article';
    }

    /**
     * By default slug will be models name.
     * This can be overridden in child model.
     *
     * @return array
     */
    public function sluggable()
    {
        return ['slug' => ['source' => 'title']];
    }
}
