<?php

namespace App\Eloquents;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
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

    public function existsXmlFile() : bool
    {
        return \Storage::exists($this->xmlFilename()) || $this->existsGzippedXmlFile();
    }

    public function existsGzippedXmlFile() : bool
    {
        return \Storage::exists($this->xmlFilename().'.gz');
    }

    public function getXmlFileAttribute() : string
    {
        if (! $this->existsXmlFile()) {
            throw new FileNotFoundException('Not Found XML File.');
        }

        if ($this->existsGzippedXmlFile()) {
            return gzdecode($this->gzipped_xml_file);
        }

        return \Storage::get($this->xmlFilename());
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

    public function getGzippedXmlFileAttribute() : string
    {
        if (! $this->existsGzippedXmlFile()) {
            throw new FileNotFoundException('Not Found Gzipped XML File.');
        }

        return \Storage::get($this->xmlFilename().'.gz');
    }

    public function getEntryPageUrlAttribute() : string
    {
        return route('entry', ['entry' => $this->uuid]);
    }

    public function getXmlFileUrlAttribute() : string
    {
        return route('entry.xml', ['entry' => $this->uuid]);
    }

    public function getJsonFileUrlAttribute() : string
    {
        return route('entry.json', ['entry' => $this->uuid]);
    }
}
