<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get products with their total stock logic 
        $products = Product::with('variants.stockMovements')->latest()->take(5)->get();
        
        $orders = Order::with('items')->latest()->take(5)->get();
        // Since we have global scoping on BaseModel, it only fetches the auth user's tenant data
        $tenant = auth()->user()->tenant;
        
        return view('dashboard', compact('products', 'orders', 'tenant'));
    }
}
