<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $tenant = $user->tenant();

        // --- CORE STATISTICS ---
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['Confirmed', 'Completed'])->sum('total_amount');

        // Stock Health (Correcting collection logic)
        $lowStockCount = Product::whereHas('variants', function ($q) {
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

        // --- EXTENDED AI INSIGHTS ---
        $aiInsights = [
            'Inventory Alert' => 'Stock behavior suggests 12% more demand for high-value items next week.',
            'Revenue Prediction' => 'Based on current trajectory, revenue is expected to grow by 18% this quarter.',
            'Optimization' => 'Recommend re-ordering best selling items to avoid stock-out.',
            'Dead Stock Warning' => 'Approximately 5% of your current inventory hasn\'t moved in 60 days; consider a clearance sale.',
            'Customer Retention' => 'Loyalty patterns indicate a 15% risk of churn for customers who haven\'t ordered in 3 weeks.',
            'Peak Hours Analysis' => 'Orders typically spike between 7:00 PM and 10:00 PM; ensure support staff is available.',
            'Product Bundling' => 'AI detected that Product A and Product C are often bought together; suggest a bundle to increase AOV.',
            'Price Sensitivity' => 'A 5% price reduction on "Category X" could potentially increase volume by 25% based on elastic demand.',
            'Supply Chain Risk' => 'Lead times for Supplier "Alpha" have increased by 4 days recently; adjust re-order points accordingly.',
            'Anomalous Activity' => 'Detected an unusual 20% surge in traffic from a specific region; could be a viral trend or a bot.',
            'Growth Opportunity' => 'Increasing marketing spend on Instagram by 10% is predicted to yield a 3x ROI based on last month\'s data.',
            'Storage Efficiency' => 'Rearranging Warehouse Zone B could improve picking speed by 14% based on item frequency.'
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
