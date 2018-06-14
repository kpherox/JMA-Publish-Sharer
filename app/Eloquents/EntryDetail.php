<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class EntryDetail extends Model
{
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
