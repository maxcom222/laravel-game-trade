<?php

namespace App\Notifications;

use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class ListingCommentNew extends Notification
{
    use Queueable;

    protected $comment;
    protected $listing;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment, $listing)
    {
        $this->comment = $comment;
        $this->listing = $listing;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'listing_id' => $this->comment->commentable_id,
            'user_id' => $this->comment->user_id,
        ];
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
            	->subject(config('settings.page_name') . ': ' . trans('emails.comment.title', ['user_name' => $this->comment->user->name, 'game_name' => $this->listing->game->name]))
              ->line(trans('emails.comment.show_comment_text', ['user_name' => $this->comment->user->name, 'game_name' => $this->listing->game->name]))
              ->action(trans('emails.comment.show_button'), $this->listing->url_slug . '#!comments')
              ->line(trans('emails.auth.thank_you_for_using_app', ['page_name' => config('settings.page_name')]));
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
            ->subject(trans('notifications.push.listing_comment_title', ['gamename' => $this->listing->game->name]))
            ->body(trans('notifications.push.listing_comment_message', ['username' => $this->comment->user->name, 'gamename' => $this->listing->game->name]))
            ->url($this->listing->url_slug . '#!comments')
            ->icon($this->listing->game->image_square);
    }
}
