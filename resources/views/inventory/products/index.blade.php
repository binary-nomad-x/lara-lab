@extends('layouts.nexus')

@section('title', 'Product List')

@section('vendor-css')
<link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
<link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
<link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">Management of Products</h5>
        <div class="action-btns">
            <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Product</span></span>
            </a>
        </div>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-products table border-top">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>SKU</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex justify-content-start align-items-center product-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar me-4 rounded-2 bg-label-secondary">
                                    <span class="avatar-initial">{{ substr($product->name, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="text-nowrap mb-0">{{ $product->name }}</h6>
                                <small class="text-muted text-truncate d-none d-sm-block">{{ $product->description }}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="text-nowrap">-</span></td>
                    <td>
                        @php $stock = $product->variants->sum(fn($v) => $v->stock); @endphp
                        <span class="badge {{ $stock <= 10 ? 'bg-label-danger' : 'bg-label-success' }}">{{ $stock }}</span>
                    </td>
                    <td><span>{{ $product->sku ?? 'N/A' }}</span></td>
                    <td><span class="badge bg-label-success">Active</span></td>
                    <td>
                        <div class="d-inline-block text-nowrap">
                            <button class="btn btn-sm btn-icon"><i class="ti ti-edit"></i></button>
                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
