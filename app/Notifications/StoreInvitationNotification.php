<?php

namespace App\Notifications;

use App\Models\StoreInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @property \App\Models\StoreInvitation $store_invitation
     */
    private $store_invitation;

    /**
     * Create a new notification instance.
     */
    public function __construct(StoreInvitation $store_invitation)
    {
        $this->afterCommit();
        $this->store_invitation = $store_invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        
        return (new MailMessage)->markdown('mail.store.send-invitation', [
            'store_invitation' => $this->store_invitation,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
