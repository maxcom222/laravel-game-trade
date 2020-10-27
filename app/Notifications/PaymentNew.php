<?php

namespace App\Notifications;

use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use money;

class PaymentNew extends Notification
{
    use Queueable;

    protected $offer;
    protected $payment;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($offer, $payment)
    {
        $this->offer = $offer;
        $this->payment = $payment;
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
            	->subject(config('settings.page_name') . ': ' . trans('emails.payment.title', ['user_name' => $this->offer->listing->user->name, 'total' => money(abs(filter_var(number_format($this->payment->total ,2), FILTER_SANITIZE_NUMBER_INT)), $this->payment->currency)->format(true), 'game_name' => $this->offer->listing->game->name, 'platform_name' => $this->offer->listing->game->platform->name]))
              ->line(trans('emails.payment.show_payment_text', ['user_name' => $this->offer->listing->user->name, 'total' => money(abs(filter_var(number_format($this->payment->total ,2), FILTER_SANITIZE_NUMBER_INT)), $this->payment->currency)->format(true), 'game_name' => $this->offer->listing->game->name, 'platform_name' => $this->offer->listing->game->platform->name]))
              ->action(trans('emails.payment.show_button'), route('frontend.offer.show', $this->offer->id))
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
            'listing_id' => $this->offer->listing_id,
            'offer_id' => $this->offer->id,
            'payment_id' => $this->payment->id,
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
            ->subject(trans('notifications.offer_paid', ['gamename' => $this->offer->listing->game->name, 'username' => $this->offer->listing->user->name]))
            ->body(trans('notifications.push.offer_paid_message', ['gamename' => $this->offer->listing->game->name, 'username' => $this->offer->listing->user->name]))
            ->url(route('frontend.offer.show', $this->offer->id))
            ->icon($this->offer->listing->game->image_square);
    }
}
