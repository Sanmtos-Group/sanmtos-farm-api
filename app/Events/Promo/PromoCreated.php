<?php

namespace App\Events\Promo;

Use App\Models\Promo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PromoCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * The promo instance.
     *
     * @var \App\Models\Promo
     */
    public $promo;

    /**
     * Create a new event instance.
     */
    public function __construct(Promo $promo)
    {
        $this->promo = $promo;
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
