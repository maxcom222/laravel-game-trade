<?php

namespace App\Notifications;

use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PriceAlert extends Notification
{
    use Queueable;

    protected $listing;
    protected $wishlist;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($listing, $wishlist)
    {
        $this->listing = $listing;
        $this->wishlist = $wishlist;
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
            return ['mail','database', OneSignalChannel::class];
        } else {
            return ['mail','database'];
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
            	->subject(trans('emails.price_alert.title', ['game_name' => $this->listing->game->name, 'platform_name' => $this->listing->game->platform->name, 'price' => $this->listing->price_formatted]) . ' - ' . config('settings.page_name'))
              ->line(trans('emails.price_alert.show_price_alert_text', ['game_name' => $this->listing->game->name, 'platform_name' => $this->listing->game->platform->name, 'price' => $this->listing->price_formatted]))
              ->action(trans('emails.price_alert.show_button'), $this->listing->url_slug)
              ->line(trans('emails.auth.thank_you_for_using_app', ['page_name' => config('settings.page_name')]));
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
            'listing_id' => $this->listing->id,
            'wishlist_id' => $this->wishlist->id,
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
            ->subject(trans('notifications.push.price_alert', ['game_name' => $this->listing->game->name, 'platform_name' => $this->listing->game->platform->name]))
            ->body(trans('notifications.push.price_alert_message', ['game_name' => $this->listing->game->name, 'platform_name' => $this->listing->game->platform->name, 'price' => $this->listing->price_formatted]))
            ->url($this->listing->url_slug)
            ->icon($this->listing->game->image_square);
    }
}
