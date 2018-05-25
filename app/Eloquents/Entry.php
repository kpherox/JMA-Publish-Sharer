<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'kind_of_info', 'feed_uuid', 'observatory_name', 'headline', 'url', 'updated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    // Relation feed
    public function feed() {
        return $this->belongsTo('App\Eloquents\Feed', 'feed_uuid', 'uuid');
    }
}
