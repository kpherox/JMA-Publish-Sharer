<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

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

    public function scopeWhereType($query, String $type)
    {
        return $query->where('url', 'LIKE', '%'.$type.'%');
    }

    // Relation entries
    public function entries() {
        return $this->hasMany('App\Eloquents\Entry', 'feed_uuid', 'uuid');
    }

    public function getTypeAttribute()
    {
        return basename(parse_url($this->url, PHP_URL_PATH), '.xml');
    }
}

