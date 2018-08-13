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

        LinkedSocialAccount::whereIn('provider_name', ['twitter', 'line'])
            ->whereHas('settings', function ($query) {
                $query->whereType('notification')->where('settings->isAllow', true);
            })->get()->each(function ($account) use ($entry) {
                try {
                    $account->notify(new EntryReceived($entry));
                } catch (\Exception $e) {
                    report($e);
                }
            });
    }
}
