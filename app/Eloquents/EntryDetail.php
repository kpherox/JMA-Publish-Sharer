<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id',
        'uuid',
        'kind_of_info',
        'url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Relation: belong to entry.
    **/
    public function entry() : BelongsTo
    {
        return $this->belongsTo('App\Eloquents\Entry');
    }

    /**
     * Use key name when route binding.
    **/
    public function getRouteKeyName() : string
    {
        return 'uuid';
    }

    public function getXmlFileAttribute()
    {
        return \Storage::get('entry/'.$this->uuid);
    }

    public function setXmlFileAttribute(string $xmlDoc)
    {
        \Storage::put('entry/'.$this->uuid, $xmlDoc);
    }
}
