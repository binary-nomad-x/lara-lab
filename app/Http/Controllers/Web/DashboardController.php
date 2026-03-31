<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        $tenant = $user->tenant;

        // --- CORE STATISTICS ---
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['Confirmed', 'Completed'])->sum('total_amount');
        $lowStockCount = Product::with('variants')->get()->filter(function($p) {
            return $p->variants->any(fn($v) => $v->stock <= 10);
        })->count();

        // --- TRENDS (MONTHLY REVENUE) ---
        $monthlyRevenue = Order::whereIn('status', ['Confirmed', 'Completed'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('SUM(total_amount) as total, strftime("%m", created_at) as month') // sqlite syntax for demo
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // --- PRODUCT PERFORMANCE ---
        $topSellingProducts = Product::with(['variants'])
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(20)
            ->get();

        // --- RECENT ACTIVITY ---
        $recentProducts = Product::with('variants')->latest()->take(20)->get();
        $recentOrders = Order::latest()->take(20)->get();

        // --- DUMMY AI INSIGHTS ---
        $aiInsights = [
            'Inventory Alert' => 'Stock behavior suggests ' . rand(5, 15) . '% more demand for electronics next week.',
            'Revenue Prediction' => 'Based on current trajectory, revenue is expected to grow by ' . rand(10, 25) . '% this quarter.',
            'Optimization' => 'Recommend re-ordering item ' . (Product::first()->name ?? 'N/A') . ' to avoid stock-out.',
        ];

        return view('dashboard', compact(
            'tenant', 
            'totalProducts', 
            'totalOrders', 
            'totalRevenue', 
            'lowStockCount',
            'recentProducts',
            'recentOrders',
            'monthlyRevenue',
            'topSellingProducts',
            'aiInsights'
        ));
    }
}
