<?php

namespace App\Eloquents;

use App\Events\EntrySaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Entry extends Model
{
    /**
     * Event map.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'updated' => EntrySaved::class,
    ];

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

    /**
     * Relation: belong to feed.
     */
    public function feed() : BelongsTo
    {
        return $this->belongsTo('App\Eloquents\Feed');
    }

    /**
     * Relation: has many entry details.
     */
    public function entryDetails() : HasMany
    {
        return $this->hasMany('App\Eloquents\EntryDetail');
    }

    /**
     * Local Scope: where observatory_name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $observatory
     */
    public function scopeOfObservatory(Builder $query, string $observatory) : Builder
    {
        $observatories = self::select('observatory_name')
                ->groupBy('observatory_name')
                ->get()
                ->filter(function ($entry) use ($observatory) {
                    $splitedObservatory = collect(preg_split('/( |　)/', $entry->observatory_name));

                    return $entry->observatory_name === $observatory || $splitedObservatory->contains($observatory);
                })->map(function ($entry) {
                    return $entry->observatory_name;
                });

        return $query->whereIn('observatory_name', $observatories);
    }

    /**
     * Mutator: parse headline.
     */
    public function getParsedHeadlineAttribute() : Collection
    {
        preg_match('/【(.*?)】(.*)/s', $this->headline, $headline);

        return collect([
            'original' => $headline[0],
            'title' => $headline[1],
            'headline' => $headline[2],
        ]);
    }

    /**
     * Mutator: entryDetails kinds.
     */
    public function getChildrenKindsAttribute() : Collection
    {
        return $this->entryDetails->sortByKind()->map(function ($detail) {
            return $detail->kind_of_info;
        });
    }

    /**
     * Mutator: entryDetails event id.
     */
    public function getEventIdAttribute() : ?string
    {
        return $this->entryDetails()->first()->event_id;
    }
}
