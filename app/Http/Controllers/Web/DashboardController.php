<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        $tenant = $user->tenant;

        // Statistics
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'Confirmed')->sum('total_amount');
        $lowStockCount = Product::with('variants')->get()->filter(function($p) {
            return $p->variants->any(fn($v) => $v->stock <= 10);
        })->count();

        $recentProducts = Product::with('variants.stockMovements')->latest()->take(5)->get();
        $recentOrders = Order::latest()->take(5)->get();

        return view('dashboard', compact(
            'tenant', 
            'totalProducts', 
            'totalOrders', 
            'totalRevenue', 
            'lowStockCount',
            'recentProducts',
            'recentOrders'
        ));
    }
}
