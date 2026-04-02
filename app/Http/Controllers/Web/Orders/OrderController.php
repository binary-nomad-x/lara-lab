<?php

namespace App\Http\Controllers\Web\Orders;

use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller {

    public function index(Request $request) {
        return view('orders.list.index', [
            'orders' => Order::latest()->paginate($request->input('pagination_size', 10))
        ]);
    }

    public function show($id) {
        return view('orders.list.show', [
            'order' => Order::with('items.variant.product')->findOrFail($id)
        ]);
    }

    public function processPayment(Request $request, $id) {
        $order = Order::findOrFail($id);

        if (in_array($order->status, ['Confirmed', 'Completed'])) {
            return back()->with('error', 'Order is already paid.');
        }

        // --- STRIPE / GATEWAY SIMULATION ---
        // Mimicking a 2-second network delay
        // Artisan::call('some-ping'); // just dummy to take time

        // todo : if payment api hit , get the boolean of response
        $success = rand(1, 10) > 1; // 90% success rate

        if ($success) {
            $order->update([
                'status' => 'Confirmed',
                'notes' => $order->notes . ' | Payment processed via Gateway (TX-' . strtoupper(Str::random(10)) . ')'
            ]);

            // Dispatch Real-time Event
            event(new OrderPaid($order));

            return back()->with('success', 'Payment successful! Order #' . substr($order->id, 0, 8) . ' is now confirmed.');
        } else {
            $order->update(['status' => 'PaymentPending']);
            return back()->with('error', 'Payment failed! Please check your payment method and try again.');
        }
    }

    public function refund(Request $request, $id) {
        $order = Order::findOrFail($id);

        if ($order->status !== 'Confirmed') {
            return back()->with('error', 'Only confirmed orders can be refunded.');
        }

        $order->update([
            'status' => 'Cancelled',
            'notes' => 'Refund processed ' . now()
        ]);

        return back()->with('success', 'Order #' . substr($order->id, 0, 8) . ' has been refunded.');
    }
}
