<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['variants'])->paginate(10);
        return view('inventory.products.index', compact('products'));
    }

    public function create()
    {
        return view('inventory.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Product::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'description' => $request->description,
        ]);

        return redirect()->route('inventory.products.index')->with('success', 'Product created successfully.');
    }
}
