<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterStatusUpdate;
use App\Eloquents\Entry;
use App\Eloquents\LinkedSocialAccount;

class EntryReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public $entry;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) : array
    {
        $via = $notifiable instanceof LinkedSocialAccount ? $notifiable : [];

        if (is_array($via)) {
            return $via;
        }

        switch ($notifiable->provider_name) {
            case 'twitter':
                $via = [TwitterChannel::class];
                break;
            default:
                $via = [];
                break;
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toTwitter($notifiable) : TwitterStatusUpdate
    {
        return new TwitterStatusUpdate($this->entry->parsed_headline['original']);
    }
}
