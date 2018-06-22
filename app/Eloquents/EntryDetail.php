<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class EntryDetail extends Model
{
    protected $fillable = [
        'entry_id',
        'kind_of_info',
        'url',
        'uuid',
        'xml_document',
    ];

    protected $hidden = [
        'xml_document',
    ];

    public function entry() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Eloquents\Entry');
    }

    public function getRouteKeyName() : String
    {
        return 'uuid';
    }
}
