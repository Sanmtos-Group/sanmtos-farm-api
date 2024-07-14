<?php

namespace App\Events\StoreInvitation;

Use App\Models\StoreInvitation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreInvitationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * The store invitation instance.
     *
     * @var \App\Models\StoreInvitation
     */
    public $store_invitation;

    /**
     * Create a new event instance.
     */
    public function __construct(StoreInvitation $store_invitation)
    {
        $this->store_invitation = $store_invitation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
