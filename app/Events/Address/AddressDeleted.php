<?php

namespace App\Events\Address;

Use App\Models\Address; 
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddressDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The address instance.
     *
     * @var \App\Models\Address
     */
    public $address;

    /**
     * Create a new event instance.
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
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
