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
        $observatories = Self::select('observatory_name')
                ->groupBy('observatory_name')
                ->get()
                ->filter(function ($entry) use ($observatory) {
                    $splitedObservatory = collect(preg_split( "/( |　)/", $entry->observatory_name));
                    return ($entry->observatory_name === $observatory || $splitedObservatory->contains($observatory));
                })->map(function ($entry) {
                    return $entry->observatory_name;
                });

        return $query->whereIn('observatory_name', $observatories);
    }

    /**
     * Relation feed
    **/
    public function feed() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Eloquents\Feed', 'feed_uuid', 'uuid');
    }

    public function entryDetails() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Eloquents\EntryDetail');
    }

    public function getParsedHeadlineAttribute() : \Illuminate\Support\Collection
    {
        preg_match('/【(.*)】(.*)/', $this->headline, $headline);
        return collect([
            'original' => $headline[0],
            'title' => $headline[1],
            'headline' => $headline[2],
        ]);
    }

    public function getChildrenKindsAttribute() : \Illuminate\Support\Collection
    {
        return $this->entryDetails->sortByKind()->map(function ($detail) {
            return $detail->kind_of_info;
        });
    }
}
