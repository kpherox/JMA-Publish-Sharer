<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class Observatory extends Model
{
    /**
     * Local Scope: where observatory_name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $observatory
     */
    public function scopeOfObservatory(Builder $query, string $observatory) : Builder
    {
        return $query->where('name', 'REGEXP', sprintf('(^| |　)%s( |　|$)', $observatory));
    }
}
