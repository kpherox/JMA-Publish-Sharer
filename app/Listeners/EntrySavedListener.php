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
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EntryReceived  $event
     * @return void
     */
    public function handle(EntrySaved $event)
    {
        $entry = $event->entry;

        LinkedSocialAccount::whereIn('provider_name', ['twitter', 'line'])->get()->each(function ($account) use ($entry) {
            $notificationSettings = $account->settings()->whereType('notification')->where('settings->isAllow', true);
            if (! $notificationSettings->exists()) {
                return;
            }

            try {
                $account->notify(new EntryReceived($entry));
            } catch (\Exception $e) {
                report($e);
            }
        });
    }
}
