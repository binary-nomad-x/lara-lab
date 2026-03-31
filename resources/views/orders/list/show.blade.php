@extends('layouts.nexus')

@section('title', 'Order Details')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
  <div class="d-flex flex-column justify-content-center">
    <div class="d-flex align-items-center mb-1">
      <h5 class="mb-0">Order #{{ substr($order->id, 0, 8) }}</h5>
      <span class="badge bg-label-success ms-3 text-uppercase">{{ $order->status }}</span>
    </div>
    <p class="mb-0">{{ $order->created_at->format('M d, Y, H:i (e)') }}</p>
  </div>
  <div class="d-flex align-content-center flex-wrap gap-2">
    @if($order->status !== 'Confirmed')
    <form action="{{ route('orders.pay', $order->id) }}" method="POST">
        @csrf
        <button class="btn btn-outline-primary" type="submit">Process Payment</button>
    </form>
    @endif
  </div>
</div>

<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">Order Details</h5>
      </div>
      <div class="card-datatable table-responsive">
        <table class="datatables-order-details table border-top">
          <thead>
            <tr>
              <th class="w-50">Product</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar-wrapper me-3">
                        <div class="avatar bg-label-secondary rounded-2"><span class="avatar-initial">{{ substr($item->variant->product->name, 0, 2) }}</span></div>
                    </div>
                    <div class="d-flex flex-column">
                        <h6 class="text-nowrap mb-0">{{ $item->variant->product->name }}</h6>
                        <small class="text-muted">{{ $item->variant->name }}</small>
                    </div>
                </div>
              </td>
              <td>{{ $order->currency_code }} {{ number_format($item->unit_price, 2) }}</td>
              <td>{{ $item->quantity }}</td>
              <td>{{ $order->currency_code }} {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-body">
            <div class="row">
                <div class="col-8"></div>
                <div class="col-4">
                    <div class="d-flex justify-content-between">
                        <span class="w-px-100">Subtotal:</span>
                        <span class="fw-medium">{{ $order->currency_code }} {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="w-px-100">Tax:</span>
                        <span class="fw-medium">{{ $order->currency_code }} 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-1 mt-1">
                        <h6 class="w-px-100 mb-0">Total:</h6>
                        <h6 class="mb-0">{{ $order->currency_code }} {{ number_format($order->total_amount, 2) }}</h6>
                    </div>
                </div>
            </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title m-0">Customer Details</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-start align-items-center mb-6">
            <div class="avatar avatar me-3">
                <img src="/assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
            </div>
            <div class="d-flex flex-column">
                <a href="#"><h6 class="mb-0">{{ Auth::user()->name }}</h6></a>
                <span>Customer ID: #{{ substr(Auth::user()->id, 0, 8) }}</span>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <h6>Contact info</h6>
        </div>
        <p class="mb-1">Email: {{ Auth::user()->email }}</p>
      </div>
    </div>
  </div>
</div>
@endsection
