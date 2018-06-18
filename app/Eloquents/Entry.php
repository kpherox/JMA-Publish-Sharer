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

    /**
     * Get route key.
    **/
    public function getRouteKeyName() : String
    {
        return 'uuid';
    }

    public function entryDetail()
    {
        return $this->hasMany('App\Eloquents\EntryDetail');
    }

    public function getParsedHeadlineAttribute()
    {
        preg_match('/【(.*)】(.*)/', $this->headline, $headline);
        return collect([
            'original' => $headline[0],
            'title' => $headline[1],
            'headline' => $headline[2],
        ]);
    }

    public function getChildrenKindsAttribute()
    {
        $res = [];
        foreach ($this->entryDetail as $detail) {
            $res[] = $detail->kind_of_info;
        }

        $kindOrder = collect(config('jmaxmlkinds'))->keys();

        return collect($res)
            ->sort(function($a, $b) use($kindOrder) {
                return ($kindOrder->search($a) > $kindOrder->search($b));
            });
    }
}
