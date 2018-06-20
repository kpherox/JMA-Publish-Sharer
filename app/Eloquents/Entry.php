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
        'feed_uuid',
        'observatory_name',
        'headline',
        'updated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function scopeWhereObservatoryName($query, String $observatory)
    {
        return $query->where('observatory_name', 'LIKE', '%'.$observatory.'%');
    }

    // Relation feed
    public function feed() {
        return $this->belongsTo('App\Eloquents\Feed', 'feed_uuid', 'uuid');
    }

    public function entryDetails()
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
        foreach ($this->entryDetails->sortByKind() as $detail) {
            $res[] = $detail->kind_of_info;
        }

        $kindOrder = collect(config('jmaxmlkinds'))->keys();

        return collect($res);
    }
}
