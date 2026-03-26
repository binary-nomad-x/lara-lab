<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|uuid|exists:variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'currency_code' => 'required|string|size:3',
        ]);

        try {
            DB::beginTransaction();
            $tenantId = auth()->user()->tenant_id;
            
            $order = Order::create([
                'tenant_id' => $tenantId,
                'status' => 'Confirmed', // Skips draft
                'currency_code' => strtoupper($validated['currency_code']),
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $variant = Variant::findOrFail($item['variant_id']);
                $lineTotal = $variant->price * $item['quantity'];
                
                OrderItem::create([
                    'tenant_id' => $tenantId,
                    'order_id' => $order->id,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $variant->price,
                    'total' => $lineTotal,
                ]);

                $totalAmount += $lineTotal;

                // Adjust stock movement
                \App\Models\StockMovement::create([
                    'tenant_id' => $tenantId,
                    'variant_id' => $item['variant_id'],
                    'quantity' => -$item['quantity'], // Negative for sale
                    'type' => 'sale',
                    'reference_id' => $order->id,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            // Double Entry Bookkeeping
            $ledger = Ledger::firstOrCreate(['name' => 'General Ledger', 'tenant_id' => $tenantId]);
            $revenueAccount = Account::firstOrCreate(['name' => 'Sales Revenue', 'type' => 'Revenue', 'tenant_id' => $tenantId]);
            $arAccount = Account::firstOrCreate(['name' => 'Accounts Receivable', 'type' => 'Asset', 'tenant_id' => $tenantId]);

            // Debiting A/R, Crediting Revenue
            JournalEntry::create([
                'tenant_id' => $tenantId,
                'ledger_id' => $ledger->id,
                'account_id' => $arAccount->id,
                'debit' => $totalAmount,
                'credit' => 0,
                'description' => "Order #{$order->id} confirmed",
            ]);

            JournalEntry::create([
                'tenant_id' => $tenantId,
                'ledger_id' => $ledger->id,
                'account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => $totalAmount,
                'description' => "Order #{$order->id} revenue",
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order processed with double-entry ledgers.',
                'order' => $order->load('items')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Order processing failed: ' . $e->getMessage()], 500);
        }
    }
}
