<?php

namespace App\Notifications;

use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RatingNew extends Notification
{
    use Queueable;

    protected $offer;
    protected $rating;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($offer, $rating, $user)
    {
        $this->offer = $offer;
        $this->rating = $rating;
        $this->user = $user;
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
            'rating' => $this->rating->rating,
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

        switch ($this->rating->rating) {
            case 0:
                $rating_str = 'negative';
                break;
            case 1:
                $rating_str = 'neutral';
                break;
            case 2:
                $rating_str = 'positive';
                break;
        }

        return OneSignalMessage::create()
            ->subject(trans('notifications.rating_new_' . $rating_str, ['username' => $this->user->name]))
            ->body(trans('notifications.push.rating_new_' . $rating_str . '_message', ['username' => $this->user->name]))
            ->url(route('frontend.offer.show', $this->offer->id))
            ->icon($this->user->avatar_square);
    }
}
