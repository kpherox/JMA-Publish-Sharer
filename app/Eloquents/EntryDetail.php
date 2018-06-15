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

    public function getParsedHeadlineAttribute() {
        preg_match('/【(.*)】(.*)/', $this->headline, $headline);
        return collect([
            'title' => $headline[1],
            'headline' => $headline[2],
        ]);
    }

    public function entry() {
        return $this->belongsTo('App\Eloquents\Entry');
    }
}
