<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Domain;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NexusDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Premium Demo Tenant
        $tenant = Tenant::create([
            'name' => 'Nexus Global Industries',
            'slug' => 'nexus-global',
            'is_active' => true,
        ]);

        Domain::create([
            'tenant_id' => $tenant->id,
            'domain' => 'nexus-global.nexuseiams.com',
            'is_primary' => true,
        ]);

        // 2. Create Staff & Admin
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'System Administrator',
            'email' => 'admin@nexus.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Owner');

        User::factory()->count(10)->create(['tenant_id' => $tenant->id]);

        // 3. Products & Variants (Inventory)
        Product::factory()->count(100)->create(['tenant_id' => $tenant->id])->each(function ($product) use ($tenant) {
            Variant::factory()->count(rand(2, 4))->create([
                'product_id' => $product->id,
                'tenant_id' => $tenant->id,
            ]);
        });

        // 4. Financial Structure (Accounting)
        $ledger = Ledger::create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Operating Ledger',
            'is_active' => true,
        ]);

        $accounts = [
            ['name' => 'Cash in Hand', 'code' => '1001', 'type' => 'Asset'],
            ['name' => 'Accounts Receivable', 'code' => '1002', 'type' => 'Asset'],
            ['name' => 'Inventory Asset', 'code' => '1003', 'type' => 'Asset'],
            ['name' => 'Sales Revenue', 'code' => '4001', 'type' => 'Revenue'],
            ['name' => 'Cost of Goods Sold', 'code' => '5001', 'type' => 'Expense'],
            ['name' => 'Operating Expenses', 'code' => '5002', 'type' => 'Expense'],
        ];

        foreach ($accounts as $acc) {
            Account::create([
                'tenant_id' => $tenant->id,
                'name' => $acc['name'],
                'code' => $acc['code'],
                'type' => $acc['type'],
            ]);
        }

        $allAccounts = Account::where('tenant_id', $tenant->id)->get();

        // 5. Orders & Financial History
        Order::factory()->count(400)->create(['tenant_id' => $tenant->id])->each(function ($order) use ($tenant, $allAccounts, $ledger) {
            $variants = Variant::where('tenant_id', $tenant->id)->inRandomOrder()->limit(rand(1, 5))->get();
            $total = 0;

            foreach ($variants as $variant) {
                $qty = rand(1, 10);
                $itemTotal = $variant->price * $qty;
                $total += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $variant->id,
                    'quantity' => $qty,
                    'unit_price' => $variant->price,
                    'total' => $itemTotal,
                ]);
            }

            $order->update(['total_amount' => $total]);

            // Create Financial Transaction for orders (if Confirmed or Completed)
            if (in_array($order->status, ['Confirmed', 'Completed'])) {
                // Debit AR or Cash
                JournalEntry::create([
                    'tenant_id' => $tenant->id,
                    'ledger_id' => $ledger->id,
                    'account_id' => $allAccounts->where('code', '1001')->first()->id,
                    'debit' => $total,
                    'credit' => 0,
                    'description' => 'Payment received for Order #' . substr($order->id, 0, 8),
                ]);

                // Credit Sales
                JournalEntry::create([
                    'tenant_id' => $tenant->id,
                    'ledger_id' => $ledger->id,
                    'account_id' => $allAccounts->where('code', '4001')->first()->id,
                    'debit' => 0,
                    'credit' => $total,
                    'description' => 'Sales revenue from Order #' . substr($order->id, 0, 8),
                ]);
            }
        });
    }
}
