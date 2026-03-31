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

        // Fix for Spatie Teams - Set the context
        setPermissionsTeamId($tenant->id);

        // 2. Create Staff & Admin
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'System Administrator',
            'email' => 'admin@nexus.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Owner');

        // Records > 500: Users
        User::factory()->count(50)->create(['tenant_id' => $tenant->id]);

        // 3. Products & Variants (Inventory)
        // Records > 500: Products
        Product::factory()->count(600)->create(['tenant_id' => $tenant->id])->each(function ($product) use ($tenant) {
            Variant::factory()->count(rand(2, 5))->create([
                'product_id' => $product->id,
                'tenant_id' => $tenant->id,
            ]);
        });
        // This will result in 1200 - 3000 Variants.

        // 4. Financial Structure (Accounting)
        $ledger = Ledger::create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Operating Ledger',
            'is_active' => true,
        ]);

        $accountData = [
            ['name' => 'Cash in Hand', 'code' => '1001', 'type' => 'Asset'],
            ['name' => 'Accounts Receivable', 'code' => '1002', 'type' => 'Asset'],
            ['name' => 'Inventory Asset', 'code' => '1003', 'type' => 'Asset'],
            ['name' => 'Sales Revenue', 'code' => '4001', 'type' => 'Revenue'],
            ['name' => 'Cost of Goods Sold', 'code' => '5001', 'type' => 'Expense'],
            ['name' => 'Operating Expenses', 'code' => '5002', 'type' => 'Expense'],
            ['name' => 'Marketing', 'code' => '5003', 'type' => 'Expense'],
            ['name' => 'Rent', 'code' => '5004', 'type' => 'Expense'],
            ['name' => 'Utilities', 'code' => '5005', 'type' => 'Expense'],
            ['name' => 'Salaries', 'code' => '5006', 'type' => 'Expense'],
        ];

        foreach ($accountData as $acc) {
            Account::create([
                'tenant_id' => $tenant->id,
                'name' => $acc['name'],
                'code' => $acc['code'],
                'type' => $acc['type'],
            ]);
        }

        $allAccounts = Account::where('tenant_id', $tenant->id)->get();

        // 5. Orders & Financial History
        // Records > 500: Orders
        Order::factory()->count(800)->create(['tenant_id' => $tenant->id])->each(function ($order) use ($tenant, $allAccounts, $ledger) {
            $variants = Variant::where('tenant_id', $tenant->id)->inRandomOrder()->limit(rand(2, 6))->get();
            $total = 0;

            foreach ($variants as $variant) {
                $qty = rand(1, 8);
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

            // Create Financial Transaction for orders
            if (in_array($order->status, ['Confirmed', 'Completed', 'Processing'])) {
                // Debit AR or Cash
                JournalEntry::create([
                    'tenant_id' => $tenant->id,
                    'ledger_id' => $ledger->id,
                    'account_id' => $allAccounts->where('code', '1001')->first()->id,
                    'debit' => $total,
                    'credit' => 0,
                    'description' => 'Order Payment: ' . substr($order->id, 0, 8),
                    'created_at' => $order->created_at,
                ]);

                // Credit Sales
                JournalEntry::create([
                    'tenant_id' => $tenant->id,
                    'ledger_id' => $ledger->id,
                    'account_id' => $allAccounts->where('code', '4001')->first()->id,
                    'debit' => 0,
                    'credit' => $total,
                    'description' => 'Revenue Recognition: ' . substr($order->id, 0, 8),
                    'created_at' => $order->created_at,
                ]);
            }
        });

        // 6. Extra Journal Entries for non-sales expenses
        // Records > 500: Journal Entries (already have 1600+ from orders, but let's add more variety)
        $expenseAccounts = $allAccounts->where('type', 'Expense');
        for ($i = 0; $i < 200; $i++) {
            $amount = rand(50, 2000);
            $acc = $expenseAccounts->random();
            JournalEntry::create([
                'tenant_id' => $tenant->id,
                'ledger_id' => $ledger->id,
                'account_id' => $acc->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => 'Monthly ' . $acc->name . ' distribution',
                'created_at' => now()->subDays(rand(1, 300)),
            ]);
            
            // Credit Cash
            JournalEntry::create([
                'tenant_id' => $tenant->id,
                'ledger_id' => $ledger->id,
                'account_id' => $allAccounts->where('code', '1001')->first()->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => 'Payment for ' . $acc->name,
                'created_at' => now()->subDays(rand(1, 300)),
            ]);
        }
    }
}
