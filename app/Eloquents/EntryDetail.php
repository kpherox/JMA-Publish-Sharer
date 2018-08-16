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
     */
    public function entry() : BelongsTo
    {
        return $this->belongsTo('App\Eloquents\Entry');
    }

    /**
     * Use key name when route binding.
     */
    public function getRouteKeyName() : string
    {
        return 'uuid';
    }

    private function xmlFilename() : string
    {
        return 'entry/'.$this->uuid;
    }

    public function isGzippedXmlFile() : bool
    {
        return \Storage::exists($this->xmlFilename().'.gz');
    }

    public function getGzippedXmlFileAttribute() : string
    {
        if ($this->isGzippedXmlFile()) {
            return \Storage::get($this->xmlFilename().'.gz');
        }

        return '';
    }

    public function getXmlFileAttribute() : string
    {
        if ($this->isGzippedXmlFile()) {
            return gzdecode($this->gzipped_xml_file);
        }

        if (\Storage::exists($this->xmlFilename())) {
            return \Storage::get($this->xmlFilename());
        }

        return '';
    }

    public function setXmlFileAttribute(string $xmlDoc)
    {
        if (config('app.supportGzip')) {
            $doc = gzencode($xmlDoc);
            \Storage::put($this->xmlFilename().'.gz', $doc);
        } else {
            \Storage::put($this->xmlFilename(), $xmlDoc);
        }
    }
}
