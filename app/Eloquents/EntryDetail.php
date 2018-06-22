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
        'kind_of_info',
        'url',
        'uuid',
        'xml_document',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'xml_document',
    ];

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
    public function getRouteKeyName() : String
    {
        return 'uuid';
    }
}
