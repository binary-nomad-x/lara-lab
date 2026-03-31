@extends('layouts.nexus')

@section('title', 'Dashboard')

@section('vendor-css')
<link rel="stylesheet" href="/assets/vendor/libs/apex-charts/apex-charts.css" />
<link rel="stylesheet" href="/assets/vendor/css/pages/cards-advance.css" />
@endsection

@section('content')
<div class="row">
    <!-- Website Analytics -->
    <div class="col-lg-12 mb-6">
        <div class="swiper-container swiper-container-horizontal swiper-dashboard-analytics card shadow-none bg-label-primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-7 ps-lg-6 ps-md-4 pt-sm-4 pb-sm-4">
                        <h4 class="card-title mb-1 text-primary">Welcome back, {{ Auth::user()->name }}! 🎉</h4>
                        <p class="mb-4">You have done <span class="fw-medium">72%</span> more sales today. Check your new badge in your profile.</p>
                        <a href="javascript:;" class="btn btn-primary">View Sales</a>
                    </div>
                    <div class="col-sm-5 text-center text-sm-end pt-sm-4 pb-sm-4">
                        <img src="/assets/img/illustrations/card-website-analytics-1.png" alt="Website Analytics" width="170" class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-lg-3 col-md-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1">{{ $totalProducts }}</h5>
                    <p class="mb-0">Total Products</p>
                </div>
                <div class="avatar">
                    <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-package ti-sm"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1">{{ $totalOrders }}</h5>
                    <p class="mb-0">Total Orders</p>
                </div>
                <div class="avatar">
                    <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-shopping-cart ti-sm"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1">${{ number_format($totalRevenue, 2) }}</h5>
                    <p class="mb-0">Revenue</p>
                </div>
                <div class="avatar">
                    <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-currency-dollar ti-sm"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 text-danger">{{ $lowStockCount }}</h5>
                    <p class="mb-0">Low Stock Alerts</p>
                </div>
                <div class="avatar">
                    <span class="avatar-initial rounded bg-label-danger"><i class="ti tabler-alert-triangle ti-sm"></i></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="col-md-6 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title m-0 me-2">Recent Inventory Movements</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="inventoryRevenue" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="inventoryRevenue">
                        <a class="dropdown-item" href="javascript:void(0);">View All</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($recentProducts as $product)
                        @foreach($product->variants as $variant)
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-secondary"><i class="ti tabler-box ti-sm"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ $variant->sku }} ({{ $variant->name }})</small>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-1">
                                        <span class="badge {{ $variant->stock <= 10 ? 'bg-label-danger' : 'bg-label-success' }}">{{ $variant->stock }} in stock</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-md-6 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title m-0 me-2">Recent Orders</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="recentOrdersDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentOrdersDropdown">
                        <a class="dropdown-item" href="javascript:void(0);">Transactions</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless border-top">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td><span class="text-body fw-medium">#{{ substr($order->id, 0, 8) }}</span></td>
                                <td><span class="badge bg-label-info">{{ $order->status }}</span></td>
                                <td class="text-success fw-medium">{{ $order->currency_code }} {{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-js')
<script src="/assets/vendor/libs/apex-charts/apexcharts.js"></script>
@endsection
