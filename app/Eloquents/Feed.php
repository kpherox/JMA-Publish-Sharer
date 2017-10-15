<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entries', 'url', 'uuid', 'updated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Not use timestamp.
     *
     * @var bool
     */
    public $timestamps = false;
}
