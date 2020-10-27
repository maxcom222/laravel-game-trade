<?php

namespace App\Notifications;

use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MessageNew extends Notification
{
    use Queueable;

    protected $offer;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($offer, $user)
    {
        $this->offer = $offer;
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
            	->subject(config('settings.page_name') . ': ' . trans('emails.message.title', ['user_name' => $this->user->name]))
              ->line(trans('emails.message.show_message_text', ['user_name' => $this->user->name]))
              ->action(trans('emails.message.show_button'), route('frontend.offer.show', $this->offer->id))
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
            'user_id' => $this->user->id,
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
            ->subject(trans('notifications.message_new', ['username' => $this->user->name]))
            ->body(trans('notifications.push.message_message', ['username' => $this->user->name]))
            ->url(route('frontend.offer.show', $this->offer->id))
            ->icon($this->user->avatar_square);
    }
}
