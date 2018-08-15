<?php

namespace App\Notifications;

use App\Eloquents\Entry;
use Illuminate\Bus\Queueable;
use App\Eloquents\LinkedSocialAccount;
use Illuminate\Notifications\Notification;
use NotificationChannels\Line\LineChannel;
use NotificationChannels\Line\LineMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterStatusUpdate;

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
        $via = [];

        if (! $notifiable instanceof LinkedSocialAccount) {
            return $via;
        }

        switch ($notifiable->provider_name) {
            case 'twitter':
                $via[] = TwitterChannel::class;
                break;
            case 'line':
                $via[] = LineChannel::class;
                break;
            default:
                break;
        }

        return $via;
    }

    /**
     * Get the twitter status of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toTwitter($notifiable) : TwitterStatusUpdate
    {
        return new TwitterStatusUpdate($this->entry->parsed_headline['original']);
    }

    /**
     * Get the line message of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toLine($notifiable)
    {
        return new LineMessage($this->entry->parsed_headline['original']);
    }
}
