<?php

namespace App\Listeners\StoreInvitation;

use App\Events\StoreInvitation\StoreInvitationCreated;
use App\Models\Role;
use App\Notifications\StoreInvitationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class StoreInvitationCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(StoreInvitationCreated $event): void
    {
        $store_invitation = $event->store_invitation;
        $store =  $store_invitation->store()->first();
        if(!is_null($store))
        {
            $user = $store_invitation->user()->first();

            if(is_null($user ))
            {
                Notification::route('mail', [
                    $store_invitation->email => '',
                ])->notify(new StoreInvitationNotification($store_invitation));
            }
            else {
                $user->notify(new StoreInvitationNotification($store_invitation));
            }

        }

    }
}
