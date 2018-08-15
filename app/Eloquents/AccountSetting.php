<?php

namespace App\Eloquents;

use App\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountSetting extends Model
{
    use PrimaryUuid;

    /**
     * Primary key column.
     */
    protected $primaryKey = 'uuid';

    /**
     * Primary key type.
     */
    protected $keyType = 'string';

    /**
     * Enable increment.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'settings',
    ];

    /**
     * The attributes defaults.
     *
     * @var array
     */
    protected $attributes = [
        'settings' => '{}',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'collection',
    ];

    /**
     * Relation: belong to linked social account.
     */
    public function account() : BelongsTo
    {
        return $this->belongsTo('App\Eloquents\LinkedSocialAccount');
    }
}
