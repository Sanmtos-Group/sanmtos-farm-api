<?php

namespace App\Events\PaymentGateway;

Use App\Models\PaymentGateway;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentGatewayCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The payment_gateway instance.
     *
     * @var \App\Models\PaymentGateway
     */
    public $payment_gateway;

    /**
     * Create a new event instance.
     */
    public function __construct(PaymentGateway $payment_gateway)
    {
        $this->payment_gateway = $payment_gateway;
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
