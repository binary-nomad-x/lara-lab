<?php

namespace App\Http\Controllers\Web\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('orders.list.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.variant.product')->findOrFail($id);
        return view('orders.list.show', compact('order'));
    }

    public function processPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (in_array($order->status, ['Confirmed', 'Completed'])) {
            return back()->with('error', 'Order is already paid.');
        }

        // --- STRIPE / GATEWAY SIMULATION ---
        // Mimicking a 2-second network delay
        // Artisan::call('some-ping'); // just dummy to take time
        
        $success = rand(1, 10) > 1; // 90% success rate
        
        if ($success) {
            $order->update([
                'status' => 'Confirmed',
                'notes' => $order->notes . ' | Payment processed via Gateway (TX-'.strtoupper(Str::random(10)).')'
            ]);
            
            // Dispatch Real-time Event
            event(new \App\Events\OrderPaid($order));

            return back()->with('success', 'Payment successful! Order #' . substr($order->id, 0, 8) . ' is now confirmed.');
        } else {
            $order->update(['status' => 'PaymentPending']);
            return back()->with('error', 'Payment failed! Please check your payment method and try again.');
        }
    }

    public function refund(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status !== 'Confirmed') {
            return back()->with('error', 'Only confirmed orders can be refunded.');
        }

        $order->update(['status' => 'Cancelled', 'notes' => 'Refund processed ' . now()]);
        
        return back()->with('success', 'Order #' . substr($order->id, 0, 8) . ' has been refunded.');
    }
}
