@extends('layouts.nexus')

@section('title', 'Enterprise Dashboard')

@section('vendor-css')
<link rel="stylesheet" href="/assets/vendor/libs/apex-charts/apex-charts.css" />
<link rel="stylesheet" href="/assets/vendor/css/pages/cards-advance.css" />
@endsection

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-lg-12 mb-6">
        <div class="card bg-label-primary shadow-none border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-7">
                        <h4 class="card-title mb-1 text-primary">Nexus Global Intelligence 👋</h4>
                        <p class="mb-4 text-body">Real-time enterprise monitoring active. You have <span class="fw-bold">{{ $totalOrders }}</span> total orders and <span class="fw-bold text-danger">{{ $lowStockCount }}</span> critical stock alerts needing attention.</p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('orders.list') }}" class="btn btn-primary btn-sm">Process Orders</a>
                            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-primary btn-sm">Manage Stock</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-end pt-3 pt-sm-0">
                        <img src="/assets/img/illustrations/card-website-analytics-1.png" alt="Analytics" width="160" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-xl-3 col-md-6 col-6 mb-6">
        <div class="card">
            <div class="card-body">
                <div class="badge p-2 bg-label-primary mb-3 rounded"><i class="ti tabler-currency-dollar ti-sm"></i></div>
                <h5 class="card-title mb-1">${{ number_format($totalRevenue / 1000, 1) }}k</h5>
                <small class="text-muted">Total Revenue</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6 mb-6">
        <div class="card">
            <div class="card-body">
                <div class="badge p-2 bg-label-success mb-3 rounded"><i class="ti tabler-package ti-sm"></i></div>
                <h5 class="card-title mb-1">{{ $totalProducts }}</h5>
                <small class="text-muted">Active Products</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6 mb-6">
        <div class="card">
            <div class="card-body">
                <div class="badge p-2 bg-label-info mb-3 rounded"><i class="ti tabler-chart-bar ti-sm"></i></div>
                <h5 class="card-title mb-1">{{ $totalOrders }}</h5>
                <small class="text-muted">Total Orders</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6 mb-6">
        <div class="card">
            <div class="card-body">
                <div class="badge p-2 bg-label-danger mb-3 rounded"><i class="ti tabler-alert-triangle ti-sm"></i></div>
                <h5 class="card-title mb-1 text-danger">{{ $lowStockCount }}</h5>
                <small class="text-muted">Critical Alerts</small>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="col-lg-8 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">Financial Performance (USD)</h5>
                <small class="text-muted">Last 6 Months</small>
            </div>
            <div class="card-body">
                <div id="revenueTrendChart"></div>
            </div>
        </div>
    </div>

    <!-- Nexus AI Insights -->
    <div class="col-lg-4 mb-6">
        <div class="card h-100 bg-gradient-primary">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2 text-white">Nexus AI Insights</h5>
                <div class="badge bg-white text-primary">Auto-Generated</div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($aiInsights as $type => $insight)
                    <li class="d-flex mb-4 pb-1 align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-secondary text-white"><i class="ti tabler-bolt"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-column">
                            <small class="text-white fw-bold">{{ $type }}</small>
                            <span class="text-white-50 small">{{ $insight }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Best Selling Products -->
    <div class="col-md-6 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Sales Leaderboard</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topSellingProducts as $product)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $product->name }}</span>
                                    <small class="text-muted">{{ $product->sku }}</small>
                                </div>
                            </td>
                            <td><span class="badge bg-label-secondary">Standard</span></td>
                            <td><span class="fw-bold">{{ $product->orders_count }}</span> Orders</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="col-md-6 mb-6">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0">Global Activity Feed</h5>
          </div>
          <div class="card-body pb-0">
            <ul class="timeline mb-0">
              @foreach($recentOrders as $order)
              <li class="timeline-item timeline-item-transparent ps-4 border-left-dashed">
                <span class="timeline-point timeline-point-primary"></span>
                <div class="timeline-event">
                  <div class="timeline-header mb-1">
                    <h6 class="mb-0">Order #{{ substr($order->id, 0, 8) }} Generated</h6>
                    <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                  </div>
                  <p class="mb-2">Total amount: {{ $order->currency_code }} {{ number_format($order->total_amount, 2) }}</p>
                  <div class="d-flex align-items-center">
                    <span class="badge bg-label-success">{{ $order->status }}</span>
                  </div>
                </div>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueTrendEl = document.querySelector('#revenueTrendChart');
    const revenueData = @json($monthlyRevenue->pluck('total'));
    const months = @json($monthlyRevenue->pluck('month'));
    
    if (revenueTrendEl) {
        const chartOptions = {
            series: [{
                name: 'Monthly Revenue',
                data: revenueData
            }],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#7367f0'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0.1,
                    stops: [0, 100]
                }
            },
            xaxis: {
                categories: months,
                axisBorder: { show: false },
                axisTicks: { show: false }
            }
        };
        const chart = new ApexCharts(revenueTrendEl, chartOptions);
        chart.render();
    }
});
</script>
@endsection
