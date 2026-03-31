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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'description' => 'nullable|string',
        ]);

        $product = Product::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? 'PRD-'.rand(100, 999),
            'description' => $validated['description'],
        ]);

        // Audit Trail Simulation
        // Log::info("Product created: " . $product->id);

        return redirect()->route('inventory.products.index')->with('success', 'Product ' . $product->name . ' created successfully.');
    }

    public function adjustStock(Request $request, $id) {
        $request->validate(['qty' => 'required|integer', 'note' => 'required|string']);
        
        $variant = Variant::findOrFail($id);
        $oldStock = $variant->stock;
        $variant->update(['stock' => $variant->stock + $request->qty]);
        
        // --- REAL STOCK MOVEMENT SIMULATION ---
        // event(new StockWasAdjusted($variant, $oldStock, $variant->stock));

        return back()->with('success', 'Stock adjusted for ' . $variant->sku . ' (New stock: ' . $variant->stock . ')');
    }
}
