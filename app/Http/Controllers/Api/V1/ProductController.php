<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('variants.stockMovements')->paginate(15);
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
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
                'sku' => $validated['variants'][0]['sku'] ?? strtoupper(Str::random(10)),
            ]);

            $createdVariants = [];
            foreach ($validated['variants'] as $v) {
                $createdVariants[] = Variant::create([
                    'product_id' => $product->id,
                    'tenant_id' => auth()->user()->tenant_id,
                    'name' => $v['name'],
                    'sku' => $v['sku'] ?? strtoupper(Str::random(12)),
                    'price' => $v['price'],
                    'cost' => $v['cost'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Product created seamlessly.',
                'product' => $product->load('variants')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Product creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return response()->json($product);
    }
}
