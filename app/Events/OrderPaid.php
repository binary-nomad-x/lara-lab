<?php

namespace App\Events;

use App\Facades\MockStripeService;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order) {


        MockStripeService::generateTransaction();


        $this->order = $order;
    }

    public function broadcastOn(): array {
        return [
            new PrivateChannel('tenant.' . $this->order->tenant_id),
        ];
    }

    public function broadcastAs() {
        return 'order.paid';
    }
}
