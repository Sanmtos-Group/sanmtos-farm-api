<?php

namespace App\Events\Value;

Use App\Models\Value;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ValueCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * The value instance.
     *
     * @var \App\Models\Value
     */
    public $value;

    /**
     * Create a new event instance.
     */
    public function __construct(Value $value)
    {
        $this->value = $value;
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
