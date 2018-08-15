<?php

namespace App\Traits;

trait PrimaryUuid
{
    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = \Uuid::generate()->string;
        });
    }
}
