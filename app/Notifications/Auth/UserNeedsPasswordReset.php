<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class UserNeedsPasswordReset
 * @package App\Notifications\Frontend\Auth
 */
class UserNeedsPasswordReset extends Notification
{
    use Queueable;
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * UserNeedsPasswordReset constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(config('settings.page_name') . ': ' . trans('emails.auth.password_reset_subject'))
            ->line(trans('emails.auth.password_cause_of_email'))
            ->action(trans('emails.auth.reset_password_button'), route('frontend.auth.password.reset.form', $this->token))
            ->line(trans('emails.auth.password_if_not_requested'));
    }
}
