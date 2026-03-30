@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="text-dark">Overview for {{ $tenant->name }}</h2>
        <p class="text-secondary">Connected Domain: <span class="badge bg-primary">{{ $tenant->domains->firstWhere('is_primary', true)->domain ?? 'No Domain' }}.nexuseiams.com</span></p>
    </div>
</div>

<div class="row">
    <!-- Inventory Overview -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
                <h5 class="card-title text-success fw-bold">Low Stock Alerts & Inventory</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">SKU</th>
                                <th>Product Name</th>
                                <th>On Hand</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td class="ps-3 text-secondary">{{ $variant->sku }}</td>
                                        <td>{{ $product->name }} <span class="badge bg-light text-dark border">{{ $variant->name }}</span></td>
                                        <td>
                                            @php
                                                $stock = collect($variant->stockMovements)->sum('quantity');
                                            @endphp
                                            <span class="badge {{ $stock <= 10 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $stock }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No products in inventory yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Orders -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
                <h5 class="card-title text-primary fw-bold">Recent Orders</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Order ID</th>
                                <th>Status</th>
                                <th>Total Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="ps-3 text-secondary">{{ substr($order->id, 0, 8) }}</td>
                                    <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                                    <td class="fw-bold">{{ $order->currency_code }} {{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-muted">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No recent orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
