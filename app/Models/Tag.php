<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $guarded = ['id'];

    /**
     * @return BelongsToMany
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
