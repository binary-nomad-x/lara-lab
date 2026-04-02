<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AuditLog;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NexusDataSeeder extends Seeder {
    public function run(): void {
        DB::disableQueryLog();
        $this->command->info("--- Nexus Intelligence Seeding System ---");

        // 1. Create Main Tenant
        $mainTenant = Tenant::create([
            'id' => (string)Str::orderedUuid(),
            'name' => 'Nexus Global Industries',
            'slug' => 'nexus-global',
            'is_active' => true,
        ]);

        Domain::create([
            'id' => (string)Str::orderedUuid(),
            'tenant_id' => $mainTenant->id,
            'domain' => 'nexus-global.nexuseiams.com',
            'is_primary' => true,
        ]);

        setPermissionsTeamId($mainTenant->id);

        User::create([
            'id' => (string)Str::orderedUuid(),
            'tenant_id' => $mainTenant->id,
            'name' => 'System Administrator',
            'email' => 'admin@nexus.com',
            'password' => Hash::make('password'),
        ])->assignRole('Owner');

        // ---------------------------------------------------------
        // 2. High-Performance Bulk Seeding (Main Tenant)
        // ---------------------------------------------------------

        // 2.1 Products
        $this->command->info("Seeding Products (600 records)");
        $countProducts = 600;
        $barProducts = $this->command->getOutput()->createProgressBar($countProducts);
        $barProducts->start();

        $productIds = [];
        $pData = [];
        for ($i = 0; $i < $countProducts; $i++) {
            $id = (string)Str::orderedUuid();
            $productIds[] = $id;
            $pData[] = [
                'id' => $id,
                'tenant_id' => $mainTenant->id,
                'name' => 'Standard ' . fake()->company() . ' ' . $i,
                'sku' => 'PRD-Bulk-' . $i . '-' . rand(100, 999),
                'description' => fake()->paragraph(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($pData) >= 100) {
                Product::insert($pData);
                $pData = [];
                $barProducts->advance(100);
            }
        }
        Product::insert($pData);
        $barProducts->finish();
        $this->command->newLine();

        // 2.2 Variants
        $this->command->info("Seeding Variants (2,500 records)");
        $countVariants = 2500;
        $barVariants = $this->command->getOutput()->createProgressBar($countVariants);
        $barVariants->start();

        $variantIds = [];
        $vData = [];
        for ($i = 0; $i < $countVariants; $i++) {
            $id = (string)Str::orderedUuid();
            $variantIds[] = $id;
            $vData[] = [
                'id' => $id,
                'tenant_id' => $mainTenant->id,
                'product_id' => $productIds[array_rand($productIds)],
                'name' => 'Bulk ' . fake()->word(),
                'sku' => 'SKU-Bulk-' . $i . '-' . rand(100, 999),
                'price' => rand(10, 1000),
                'cost' => rand(5, 500),
                'stock' => rand(0, 1000),
                'attributes' => json_encode(['Color' => fake()->safeColorName, 'Size' => 'L']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($vData) >= 200) {
                Variant::insert($vData);
                $vData = [];
                $barVariants->advance(200);
            }
        }
        Variant::insert($vData);
        $barVariants->finish();
        $this->command->newLine();

        // 2.3 Financial Setup
        $this->command->info("Setting up Accounts and Ledger");
        $ledger = Ledger::create([
            'id' => (string)Str::orderedUuid(),
            'tenant_id' => $mainTenant->id,
            'name' => 'Nexus Operating Ledger',
            'is_active' => true,
        ]);

        $accs = [];
        $types = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        foreach (range(1, 100) as $i) {
            $accs[] = Account::create([
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $mainTenant->id,
                'name' => 'Bulk Account ' . $i,
                'code' => 'ACC-' . $i,
                'type' => $types[array_rand($types)],
            ]);
        }

        // 2.4 Orders
        $this->command->info("Seeding Orders (1,000 records)");
        $countOrders = 1000;
        $barOrders = $this->command->getOutput()->createProgressBar($countOrders);
        $barOrders->start();

        $orderIds = [];
        $orderData = [];
        for ($i = 0; $i < $countOrders; $i++) {
            $id = (string)Str::orderedUuid();
            $orderIds[] = $id;
            $orderData[] = [
                'id' => $id,
                'tenant_id' => $mainTenant->id,
                'status' => fake()->randomElement(['Draft', 'Confirmed', 'Completed', 'Processing']),
                'total_amount' => rand(100, 10000),
                'currency_code' => 'USD',
                'notes' => fake()->sentence(),
                'created_at' => now()->subDays(rand(1, 300)),
                'updated_at' => now(),
            ];
            if (count($orderData) >= 100) {
                Order::insert($orderData);
                $orderData = [];
                $barOrders->advance(100);
            }
        }
        Order::insert($orderData);
        $barOrders->finish();
        $this->command->newLine();

        // 2.5 Order Items & Financial Distribution
        $this->command->info("Seeding Order Items and Ledger entries (5,000+ records)");
        $countItems = 4000;
        $barItems = $this->command->getOutput()->createProgressBar($countItems);
        $barItems->start();

        $itemsData = [];
        $journalData = [];
        for ($i = 0; $i < $countItems; $i++) {
            $lineTotal = rand(50, 2000);
            $itemsData[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $mainTenant->id,
                'order_id' => $orderIds[array_rand($orderIds)],
                'variant_id' => $variantIds[array_rand($variantIds)],
                'quantity' => rand(1, 5),
                'unit_price' => rand(10, 500),
                'total' => $lineTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Journal Entry chunk (Debit)
            $journalData[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $mainTenant->id,
                'ledger_id' => $ledger->id,
                'account_id' => $accs[array_rand($accs)]->id,
                'debit' => $lineTotal,
                'credit' => 0,
                'description' => 'System Bulk Entry ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($itemsData) >= 500) {
                OrderItem::insert($itemsData);
                JournalEntry::insert($journalData);
                $itemsData = [];
                $journalData = [];
                $barItems->advance(500);
            }
        }
        OrderItem::insert($itemsData);
        JournalEntry::insert($journalData);
        $barItems->finish();
        $this->command->newLine();

        $this->command->info("Seeding Audit Logs (1,000 records)");
        AuditLog::factory()->count(1000)->create(['tenant_id' => $mainTenant->id]);

        $this->command->info("Nexus Intelligent Data Seed Complete. Final record count > 15,000.");
    }
}
