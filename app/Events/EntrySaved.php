<?php

namespace App\Events;

use App\Eloquents\Entry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntrySaved
{
    use Dispatchable, SerializesModels;

    public $entry;

    /**
     * Create a new event instance.
     *
     * @param  \App\Eloquents\Entry $entry
     * @return void
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }
}
