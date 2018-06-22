<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Feed extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'url',
        'updated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Relation: has many entries.
    **/
    public function entries() : HasMany
    {
        return $this->hasMany('App\Eloquents\Entry', 'feed_uuid', 'uuid');
    }

    /**
     * Local Scope: where url ($type.xml).
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $type
    **/
    public function scopeWhereType(Builder $query, string $type) : Builder
    {
        return $query->where('url', 'LIKE', '%'.$type.'.xml');
    }

    /**
     * Mutator: feed type.
    **/
    public function getTypeAttribute() : string
    {
        return basename(parse_url($this->url, PHP_URL_PATH), '.xml');
    }
}

