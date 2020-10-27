<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class UserNeedsConfirmation
 * @package App\Notifications\Frontend\Auth
 */
class UserNeedsConfirmation extends Notification
{
    use Queueable;

    /**
     * @var
     */
    protected $confirmation_code;

    /**
     * UserNeedsConfirmation constructor.
     * @param $confirmation_code
     */
    public function __construct($confirmation_code)
    {
        $this->confirmation_code = $confirmation_code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject(config('settings.page_name') . ': ' . trans('auth.confirmation.confirm'))
            ->line(trans('emails.auth.click_to_confirm'))
            ->action(trans('emails.auth.confirm_account'), route('frontend.auth.account.confirm', $this->confirmation_code))
            ->line(trans('emails.auth.thank_you_for_using_app', ['page_name' => config('settings.page_name')]));
    }
}
