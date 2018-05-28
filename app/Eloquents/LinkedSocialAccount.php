<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkedSocialAccount extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_name', 'provider_id', 'account_name', 'account_avatar', 'account_token', 'account_token_secret'
    ];

    /**
     * Relation user
    **/
    public function user() : BelongsTo
    {
        return $this->belongsTo('App\Eloquents\User');
    }
}
