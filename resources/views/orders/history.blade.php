@extends('layouts.nexus')

@section('title', 'Order Transactions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">Detailed Order History</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm"><i class="ti ti-download me-1"></i> Export</button>
                </div>
            </div>
            <div class="card-body border-top p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>#{{ substr($order->id, 0, 8) }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>{{ Auth::user()->name }}</td>
                            <td class="fw-bold">{{ $order->currency_code }} {{ number_format($order->total_amount, 2) }}</td>
                            <td>{!! \App\Helpers\NexusHelper::getStatusBadge($order->status) !!}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('orders.show', $order->id) }}">View Details</a></li>
                                        @if($order->status === 'Confirmed')
                                        <li><form action="{{ route('orders.refund', $order->id) }}" method="POST">@csrf<button class="dropdown-item text-danger">Refund</button></form></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mx-5 my-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
