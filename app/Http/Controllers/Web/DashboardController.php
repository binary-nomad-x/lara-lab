<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        
        $tenant = $user->tenant;

        // --- CORE STATISTICS ---
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['Confirmed', 'Completed'])->sum('total_amount');
        
        // Stock Health (Correcting collection logic)
        $lowStockCount = Product::whereHas('variants', function($q) {
            $q->where('stock', '<=', 10);
        })->count();

        // --- TRENDS (MONTHLY REVENUE) - Postgres Compatible ---
        $monthlyRevenue = Order::whereIn('status', ['Confirmed', 'Completed'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('SUM(total_amount) as total, to_char(created_at, \'MM\') as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // --- PRODUCT PERFORMANCE ---
        $topSellingProducts = Product::with(['variants'])
            ->withCount('orderItems') // products don't have orders directly in some schemas, using items
            ->orderBy('order_items_count', 'desc')
            ->take(20)
            ->get();

        // --- RECENT ACTIVITY ---
        $recentProducts = Product::with('variants')->latest()->take(20)->get();
        $recentOrders = Order::latest()->take(20)->get();

        // --- DUMMY AI INSIGHTS ---
        $aiInsights = [
            'Inventory Alert' => 'Stock behavior suggests 12% more demand for high-value items next week.',
            'Revenue Prediction' => 'Based on current trajectory, revenue is expected to grow by 18% this quarter.',
            'Optimization' => 'Recommend re-ordering best selling items to avoid stock-out.',
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
