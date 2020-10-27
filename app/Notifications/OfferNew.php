<?php

namespace App\Notifications;

use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OfferNew extends Notification
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
            return ['database','mail', OneSignalChannel::class];
        } else {
            return ['database','mail'];
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
        if (is_null($this->offer->price_offer)) {
            return (new MailMessage)
              	->subject(config('settings.page_name') . ': ' . trans('emails.offer.title', ['game_name' => $this->offer->listing->game->name, 'platform_name' => $this->offer->listing->game->platform->name, 'user_name' => $this->offer->user->name]))
                ->line(trans('emails.offer.trade_text', ['game_name' => $this->offer->listing->game->name, 'platform_name' => $this->offer->listing->game->platform->name, 'user_name' => $this->offer->user->name, 'trade_name' => $this->offer->game->name, 'trade_platform' => $this->offer->game->platform->name]))
                ->action(trans('emails.offer.show_button'), route('frontend.offer.show', $this->offer->id))
                ->line(trans('emails.auth.thank_you_for_using_app', ['page_name' => config('settings.page_name')]));
        } else {
            return (new MailMessage)
                ->subject(config('settings.page_name') . ': ' . trans('emails.offer.title', ['game_name' => $this->offer->listing->game->name, 'platform_name' => $this->offer->listing->game->platform->name, 'user_name' => $this->offer->user->name]))
                ->line(trans('emails.offer.buy_text', ['game_name' => $this->offer->listing->game->name, 'platform_name' => $this->offer->listing->game->platform->name, 'user_name' => $this->offer->user->name, 'price' => $this->offer->price_offer_formatted]))
                ->action(trans('emails.offer.show_button'), route('frontend.offer.show', $this->offer->id))
                ->line(trans('emails.auth.thank_you_for_using_app', ['page_name' => config('settings.page_name')]));
        }
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
            'trade' => is_null($this->offer->price_offer) ? '1' : '0',
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
            ->subject(is_null($this->offer->price_offer) ? trans('notifications.offer_new_trade', ['gamename' => $this->offer->listing->game->name, 'tradegame' => $this->offer->game->name, 'username' => $this->offer->user->name]) : trans('notifications.offer_new_buy', ['gamename' => $this->offer->listing->game->name, 'price' => $this->offer->price_offer_formatted, 'username' => $this->offer->user->name]))
            ->body(is_null($this->offer->price_offer) ? trans('notifications.push.offer_new_trade_message', ['gamename' => $this->offer->listing->game->name, 'tradegame' => $this->offer->game->name, 'username' => $this->offer->user->name]) : trans('notifications.push.offer_new_buy_message', ['gamename' => $this->offer->listing->game->name, 'price' => $this->offer->price_offer_formatted, 'username' => $this->offer->user->name]))
            ->url(route('frontend.offer.show', $this->offer->id))
            ->icon($this->offer->listing->game->image_square);
    }
}
