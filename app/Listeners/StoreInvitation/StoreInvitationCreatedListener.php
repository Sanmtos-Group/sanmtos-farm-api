<?php

namespace App\Listeners\StoreInvitation;

use App\Events\StoreInvitation\StoreInvitationCreated;
use App\Models\Role;
use App\Notifications\StoreInvitation;
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
        // $this->afterCommit();
    }

    /**
     * Handle the event.
     */
    public function handle(StoreInvitationCreated $event): void
    {
        $store_invitation = $event->store_invitation;

        if(!is_null($store = $store_invitation->store))
        {

            if(is_null($user = $store_invitation->user))
            {
                Notification::route('mail', [
                    $store_invitation->email => '',
                ])->notify(new StoreInvitation($store_invitation));
            }
            else {
                $user->notify(new StoreInvitation($store_invitation));
            }

        }

    }
}
