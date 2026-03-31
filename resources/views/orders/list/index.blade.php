@extends('layouts.nexus')

@section('title', 'Order List')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="card-title">All Customer Orders</h5>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-order table border-top">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('orders.show', $order->id) }}" class="text-body fw-bold">#{{ substr($order->id, 0, 8) }}</a></td>
                    <td><span class="text-nowrap">{{ $order->created_at->format('M d, Y, H:i') }}</span></td>
                    <td><span class="text-nowrap">{{ $order->currency_code }} {{ number_format($order->total_amount, 2) }}</span></td>
                    <td>
                        @php
                            $badge = match($order->status) {
                                'Confirmed' => 'bg-label-success',
                                'Draft' => 'bg-label-warning',
                                default => 'bg-label-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ $order->status }}</span>
                    </td>
                    <td>
                        <div class="d-inline-block text-nowrap">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-icon"><i class="ti ti-eye"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
