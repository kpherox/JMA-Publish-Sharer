<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class EntryDetail extends Model
{
    protected $fillable = [
        'entry_id',
        'kind_of_info',
        'feed_uuid',
        'url',
        'uuid',
        'xml_document',
    ];

    protected $hidden = [
        'xml_document',
    ];

    public function entry()
    {
        return $this->belongsTo('App\Eloquents\Entry');
    }
}
