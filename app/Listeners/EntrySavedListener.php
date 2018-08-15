<?php

namespace App\Listeners;

use App\Events\EntrySaved;
use App\Notifications\EntryReceived;
use App\Eloquents\LinkedSocialAccount;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EntrySavedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  EntryReceived  $event
     * @return void
     */
    public function handle(EntrySaved $event)
    {
        $entry = $event->entry;

        $feedtypesFilter = function ($query) use ($entry) {
            $query
                ->where(function ($query) {
                    $query->where('settings->filters->feedtypes->isAllow', false)
                        ->orWhereNull('settings->filters->feedtypes->isAllow');
                })->orWhere(function ($query) use ($entry) {
                    $query->where('settings->filters->feedtypes->isAllow', true)
                        ->where('settings->filters->feedtypes->'.$entry->feed->type, true);
                });
        };

        LinkedSocialAccount::whereIn('provider_name', ['twitter', 'line'])
            ->whereHas('settings', function ($query) use ($feedtypesFilter) {
                $query->whereType('notification')
                    ->where('settings->isAllow', true)
                    ->where($feedtypesFilter);
            })->get()->each(function ($account) use ($entry) {
                try {
                    $account->notify(new EntryReceived($entry));
                } catch (\Exception $e) {
                    report($e);
                }
            });
    }
}
