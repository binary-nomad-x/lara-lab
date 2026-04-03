<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\ConflictLog;
use App\Models\Currency;
use App\Models\DeviceRegistry;
use App\Models\Domain;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\Shipment;
use App\Models\StockMovement;
use App\Models\Subscription;
use App\Models\SyncQueue;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Variant;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class NexusDataSeeder extends Seeder {
    public function run(): void {

        DB::disableQueryLog();
        $this->command->info("--- Nexus Intelligence Seeding System ---");

        // Initialize Faker
        $faker = Faker::create();

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
        $this->seedProducts($mainTenant, $faker);

        // 2.2 Variants
        $this->seedVariants($mainTenant, $faker);

        // 2.3 Batches
        $this->seedBatches($mainTenant, $faker);

        // 2.4 Stock Movements
        $this->seedStockMovements($mainTenant, $faker);

        // 2.5 Orders
        $this->seedOrders($mainTenant, $faker);

        // 2.6 Order Items
        $this->seedOrderItems($mainTenant, $faker);

        // 2.7 Currencies
        $this->seedCurrencies($mainTenant, $faker);

        // 2.8 Accounts
        $this->seedAccounts($mainTenant, $faker);

        // 2.9 Ledgers
        $this->seedLedgers($mainTenant, $faker);

        // 2.10 Journal Entries
        $this->seedJournalEntries($mainTenant, $faker);

        // 2.11 Audit Logs
        $this->seedAuditLogs($mainTenant, $faker);

        // 2.12 Device Registries
        $this->seedDeviceRegistries($mainTenant, $faker);

        // 2.13 Sync Queues
        $this->seedSyncQueues($mainTenant, $faker);

        // 2.14 Activity History
        $this->seedActivity($mainTenant, $faker);

        // 2.15 Conflict Logs
        $this->seedConflictLogs($mainTenant, $faker);

        // 2.16 Product Returns
        $this->seedProductReturns($mainTenant, $faker);

        // 2.17 Shipments
        $this->seedShipments($mainTenant, $faker);

        // 2.18 Subscriptions
        $this->seedSubscriptions($mainTenant, $faker);

        $this->command->info("Nexus Intelligent Data Seed Complete. Final record count > 15,000.");
    }

    private function seedProducts($tenant, $faker): void {
        $this->command->info("Seeding Products (600 records)");
        $count = 600;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'name' => 'Standard ' . $faker->company() . ' ' . $i,
                'sku' => 'PRD-Bulk-' . $i . '-' . rand(100, 999),
                'description' => $faker->paragraph(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 100) {
                Product::insert($data);
                $data = [];
                $bar->advance(100);
            }
        }
        Product::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedVariants($tenant, $faker): void {
        $this->command->info("Seeding Variants (2,500 records)");
        $count = 2500;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $productIds = Product::pluck('id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'product_id' => $productIds[array_rand($productIds)],
                'name' => 'Bulk ' . $faker->word(),
                'sku' => 'SKU-Bulk-' . $i . '-' . rand(100, 999),
                'price' => rand(10, 1000),
                'cost' => rand(5, 500),
                'stock' => rand(0, 1000),
                'attributes' => json_encode(['Color' => $faker->safeColorName(), 'Size' => 'L']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 200) {
                Variant::insert($data);
                $data = [];
                $bar->advance(200);
            }
        }
        Variant::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedBatches($tenant, $faker): void {
        $this->command->info("Seeding Batches (1,000 records)");
        $count = 1000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $variantIds = Variant::pluck('id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'variant_id' => $variantIds[array_rand($variantIds)],
                'batch_number' => 'BATCH-' . $i . '-' . rand(100, 999),
                'expiry_date' => $faker->optional(0.7)->dateTimeBetween('+1 month', '+2 years'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 100) {
                Batch::insert($data);
                $data = [];
                $bar->advance(100);
            }
        }
        Batch::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedStockMovements($tenant, $faker): void {
        $this->command->info("Seeding Stock Movements (5,000 records)");
        $count = 5000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $variantIds = Variant::pluck('id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'variant_id' => $variantIds[array_rand($variantIds)],
                'quantity' => rand(-100, 100), // Negative for out, positive for in
                'type' => $faker->randomElement(['purchase', 'sale', 'adjustment', 'return']),
                'reference_id' => $faker->optional(0.8)->uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 500) {
                StockMovement::insert($data);
                $data = [];
                $bar->advance(500);
            }
        }
        StockMovement::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedOrders($tenant, $faker): void {
        $this->command->info("Seeding Orders (1,000 records)");
        $count = 1000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'status' => $faker->randomElement(['Draft', 'Confirmed', 'Completed', 'Processing']),
                'total_amount' => rand(100, 10000),
                'currency_code' => 'USD',
                'notes' => $faker->sentence(),
                'created_at' => now()->subDays(rand(1, 300)),
                'updated_at' => now(),
            ];
            if (count($data) >= 100) {
                Order::insert($data);
                $data = [];
                $bar->advance(100);
            }
        }
        Order::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedOrderItems($tenant, $faker): void {
        $this->command->info("Seeding Order Items (4,000 records)");
        $count = 4000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $orderIds = Order::pluck('id')->toArray();
        $variantIds = Variant::pluck('id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $lineTotal = rand(50, 2000);
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'order_id' => $orderIds[array_rand($orderIds)],
                'variant_id' => $variantIds[array_rand($variantIds)],
                'quantity' => rand(1, 5),
                'unit_price' => rand(10, 500),
                'total' => $lineTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 500) {
                OrderItem::insert($data);
                $data = [];
                $bar->advance(500);
            }
        }
        OrderItem::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedCurrencies($tenant, $faker): void {
        $this->command->info("Seeding Currencies (5 records)");
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'INR'];
        foreach ($currencies as $code) {
            Currency::create([
                'id' => (string)Str::orderedUuid(),
                'code' => $code,
                'name' => $faker->currencyCode(),
                'exchange_rate' => rand(0.5, 2.5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->newLine();
    }

    private function seedAccounts($tenant, $faker): void {
        $this->command->info("Seeding Accounts (100 records)");
        $types = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'name' => 'Bulk Account ' . $i,
                'code' => 'ACC-' . $i,
                'type' => $types[array_rand($types)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Account::insert($data);
        $this->command->newLine();
    }

    private function seedLedgers($tenant, $faker): void {
        $this->command->info("Seeding Ledgers (5 records)");
        $ledgers = ['Operating Ledger', 'Sales Ledger', 'Purchase Ledger', 'General Ledger', 'Tax Ledger'];
        foreach ($ledgers as $ledger) {
            Ledger::create([
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'name' => $ledger,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->newLine();
    }

    private function seedJournalEntries($tenant, $faker): void {
        $this->command->info("Seeding Journal Entries (5,000 records)");
        $count = 5000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $accountIds = Account::pluck('id')->toArray();
        $ledgerIds = Ledger::pluck('id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $amount = rand(10, 1000);
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'ledger_id' => $ledgerIds[array_rand($ledgerIds)],
                'account_id' => $accountIds[array_rand($accountIds)],
                'debit' => $faker->boolean() ? $amount : 0,
                'credit' => $faker->boolean() ? $amount : 0,
                'description' => 'System Bulk Entry ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 500) {
                JournalEntry::insert($data);
                $data = [];
                $bar->advance(500);
            }
        }
        JournalEntry::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedAuditLogs($tenant, $faker): void {
        $this->command->info("Seeding Audit Logs (1,000 records)");
        $count = 1000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $userIds = User::pluck('id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'user_id' => $userIds[array_rand($userIds)] ?? null,
                'action' => $faker->randomElement(['create', 'update', 'delete']),
                'table_name' => $faker->randomElement(['users', 'products', 'orders', 'variants']),
                'payload_before' => json_encode(['key' => 'value']),
                'payload_after' => json_encode(['key' => 'new_value']),
                'ip_address' => $faker->ipv4(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 100) {
                AuditLog::insert($data);
                $data = [];
                $bar->advance(100);
            }
        }
        AuditLog::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedDeviceRegistries($tenant, $faker): void {
        $this->command->info("Seeding Device Registries (100 records)");
        $count = 100;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $userIds = User::pluck('id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'user_id' => $userIds[array_rand($userIds)] ?? null,
                'device_id' => 'DEVICE-' . $i . '-' . rand(100, 999),
                'device_name' => $faker->word(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 20) {
                DeviceRegistry::insert($data);
                $data = [];
                $bar->advance(20);
            }
        }
        DeviceRegistry::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedSyncQueues($tenant, $faker): void {
        $this->command->info("Seeding Sync Queues (1,000 records)");
        $count = 1000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $deviceIds = DeviceRegistry::pluck('device_id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'device_id' => $deviceIds[array_rand($deviceIds)],
                'entity' => $faker->randomElement(['product', 'order', 'variant']),
                'action' => $faker->randomElement(['create', 'update', 'delete']),
                'payload' => json_encode(['key' => 'value']),
                'synced_at' => $faker->optional(0.7)->dateTimeThisYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 100) {
                SyncQueue::insert($data);
                $data = [];
                $bar->advance(100);
            }
        }
        SyncQueue::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedActivity($tenant, $faker): void {
        $this->command->info("Seeding Activity History (1,000 records)");
        $count = 1000;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $deviceIds = DeviceRegistry::pluck('device_id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'device_id' => $deviceIds[array_rand($deviceIds)],
                'data' => json_encode(['activity' => $faker->sentence()]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 100) {
                Activity::insert($data);
                $data = [];
                $bar->advance(100);
            }
        }
        Activity::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedConflictLogs($tenant, $faker): void {
        $this->command->info("Seeding Conflict Logs (500 records)");
        $count = 500;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $deviceIds = DeviceRegistry::pluck('device_id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'device_id' => $deviceIds[array_rand($deviceIds)],
                'data' => json_encode(['conflict' => $faker->sentence()]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 50) {
                ConflictLog::insert($data);
                $data = [];
                $bar->advance(50);
            }
        }
        ConflictLog::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedProductReturns($tenant, $faker): void {
        $this->command->info("Seeding Product Returns (500 records)");
        $count = 500;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $deviceIds = DeviceRegistry::pluck('device_id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'device_id' => $deviceIds[array_rand($deviceIds)],
                'data' => json_encode(['return_reason' => $faker->sentence()]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 50) {
                ProductReturn::insert($data);
                $data = [];
                $bar->advance(50);
            }
        }

        ProductReturn::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedShipments($tenant, $faker): void {
        $this->command->info("Seeding Shipments (500 records)");
        $count = 500;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        $deviceIds = DeviceRegistry::pluck('device_id')->toArray();
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'device_id' => $deviceIds[array_rand($deviceIds)],
                'data' => json_encode(['shipment_status' => $faker->randomElement(['pending', 'shipped', 'delivered'])]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($data) >= 50) {
                Shipment::insert($data);
                $data = [];
                $bar->advance(50);
            }
        }
        Shipment::insert($data);
        $bar->finish();
        $this->command->newLine();
    }

    private function seedSubscriptions($tenant, $faker): void {
        $this->command->info("Seeding Subscriptions (5 records)");
        $plans = ['Basic', 'Pro', 'Enterprise', 'Premium', 'Free Trial'];
        foreach ($plans as $plan) {
            Subscription::create([
                'id' => (string)Str::orderedUuid(),
                'tenant_id' => $tenant->id,
                'plan_name' => $plan,
                'trial_ends_at' => $faker->optional(0.7)->dateTimeBetween('+1 week', '+1 month'),
                'ends_at' => $faker->optional(0.5)->dateTimeBetween('+3 months', '+1 year'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->newLine();
    }
}