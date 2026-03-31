<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {
    public function index(Request $request) {
        return response()->json([
            'data' => Product::with('variants.stockMovements')->paginate($request->input('page_size', 20))
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Note: tenant_id is auto-injected via the BaseModel scopes based on auth user
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'sku' => $validated['variants'][0]['sku'] ?? generateSku(),
            ]);

            foreach ($validated['variants'] as $variant) {
                Variant::create([
                    'product_id' => $product->id,
                    'tenant_id' => auth()->user()->tenant_id,
                    'name' => $variant['name'],
                    'sku' => $variant['sku'] ?? generateSku(),
                    'price' => $variant['price'],
                    'cost' => $variant['cost'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Product created seamlessly.',
                'product' => $product->load('variants')
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Product creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($id) {
        $product = Product::with('variants')->findOrFail($id);
        return response()->json($product);
    }
}
