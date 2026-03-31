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
        
        $order->update(['status' => 'Confirmed']);
        
        // Trigger real-time notification
        event(new \App\Events\OrderPaid($order));

        return back()->with('success', 'Payment processed successfully for order #' . substr($order->id, 0, 8));
    }
}
