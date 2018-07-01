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

    public function getXmlFilenameAttribute()
    {
        return 'entry/'.$this->uuid;
    }

    public function getIsGzippedXmlFileAttribute()
    {
        return \Storage::exists($this->xml_filename.'.gz');
    }

    public function getGunzippedXmlFileAttribute()
    {
        if ($this->is_gzipped_xml_file) {
            return gzdecode($this->xml_file);
        } else {
            return $this->xml_file;
        }
    }

    public function getXmlFileAttribute()
    {
        if ($this->is_gzipped_xml_file) {
            return \Storage::get($this->xml_filename.'.gz');
        }

        if (\Storage::exists($this->xml_filename)) {
            return \Storage::get($this->xml_filename);
        }

        return null;
    }

    public function setXmlFileAttribute(string $xmlDoc)
    {
        if (config('app.supportGzip')) {
            $doc = gzencode($xmlDoc);
            \Storage::put($this->xml_filename.'.gz', $doc);
        } else {
            \Storage::put($this->xml_filename, $xmlDoc);
        }
    }
}
