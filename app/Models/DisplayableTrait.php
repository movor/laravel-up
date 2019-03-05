<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Vkovic\LaravelDbRedirector\Models\RedirectRule;

trait DisplayableTrait
{
    use Sluggable;

    public static function bootDisplayableTrait()
    {
        static::updated(function (Model $model) {
            // Create 301 redirect when slug changes
            if ($model->isDirty('slug')) {
                $originalModel = $model::hydrate([$model->getOriginal()])->first();

                RedirectRule::create([
                    'origin' => $originalModel->getUri(),
                    'destination' => $model->getUri()
                ]);
            }
        });

        static::deleted(function (Model $model) {
            // Remove redirects when model is deleted
            try {
                RedirectRule::deleteChainedRecursively($model->getUri());
            } catch (\Exception $e) {
                // ...
            }
        });
    }

    /**
     * By default slug will be models name.
     * This can be overridden in child model.
     *
     * @return array
     */
    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    /**
     * Get base URI (e.g 'articles')
     *
     * @return string
     */
    abstract public static function getBaseUri();

    /**
     * Get URI (e.g articles/some-cool-article)
     *
     * @return string
     */
    public function getUri()
    {
        return ltrim(self::getBaseUri() . '/' . $this->slug, '/');
    }

    /**
     * Get URL (e.g. http://website.com/articles/some-cool-article)
     *
     * @return string
     */
    public function getUrl()
    {
        return url($this->getUri());
    }
}