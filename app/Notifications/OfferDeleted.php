<?php

namespace App\Notifications;

use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OfferDeleted extends Notification
{
    use Queueable;

    protected $offer;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (config('settings.onesignal')) {
            return ['database', OneSignalChannel::class];
        } else {
            return ['database'];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'listing_id' => $this->offer->listing_id,
            'offer_id' => $this->offer->id,
        ];
    }

    /**
     * Send OneSignal Web Push Notification to user.
     *
     * @param  mixed  $notifiable
     * @return OneSignalMessage
     */
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(trans('notifications.offer_deleted', ['gamename' => $this->offer->listing->game->name, 'username' => $this->offer->user->name]))
            ->body(trans('notifications.push.offer_deleted_message', ['gamename' => $this->offer->listing->game->name, 'username' => $this->offer->user->name]))
            ->url(route('frontend.offer.show', $this->offer->id))
            ->icon($this->offer->listing->game->image_square);
    }
}
