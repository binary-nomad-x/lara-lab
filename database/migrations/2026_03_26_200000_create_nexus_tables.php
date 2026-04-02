<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Tenancy Core
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('plan_name');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Extend Users table for tenancy (assuming Users already exists, but I will modify the default user migration or just add it here)
        // Note: Default Laravel create_users_table migration exists. I'll modify that via replace_file_content or add a column here.
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->uuid('tenant_id')->nullable()->after('status');
                // The foreign key constraint won't work on 'users' id if it is an int/bigint and tenants.id is UUID unless we cast or drop and recreate. 
                // A better approach is dropping users table and recreating it, but wait, default auth uses bigint ids. Let's make user uuid as well.
            }
        });

        // 2. Inventory
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('product_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 19, 4);
            $table->decimal('cost', 19, 4)->nullable();
            $table->integer('stock')->default(0);
            $table->jsonb('attributes')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('variant_id');
            $table->string('batch_number');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('variant_id');
            $table->integer('quantity'); // Positive for in, negative for out
            $table->string('type'); // purchase, sale, adjustment, return
            $table->string('reference_id')->nullable();
            $table->timestamps(); // partitioned by date eventually
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');

            $table->index(['tenant_id', 'variant_id']);
        });

        // 3. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('status'); // Draft, Confirmed, etc.
            $table->decimal('total_amount', 19, 4)->default(0);
            $table->string('currency_code', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->jsonb('snapshot')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('order_id');
            $table->uuid('variant_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 19, 4);
            $table->decimal('total', 19, 4);
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
        });

        // 4. Finance
        Schema::create('currencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->decimal('exchange_rate', 19, 6)->default(1);
            $table->timestamps();
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type'); // Asset, Liability, Equity, Revenue, Expense
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('ledger_id');
            $table->uuid('account_id');
            $table->decimal('debit', 19, 4)->default(0);
            $table->decimal('credit', 19, 4)->default(0);
            $table->string('description')->nullable();
            $table->timestamps(); // Immutable append-only
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('ledger_id')->references('id')->on('ledgers')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        // 5. Audit & Sync
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('table_name');
            $table->jsonb('payload_before')->nullable();
            $table->jsonb('payload_after')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('device_registries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('device_id')->unique(); // PWA sync token
            $table->string('device_name')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('sync_queues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('device_id');
            $table->string('entity');
            $table->string('action');
            $table->jsonb('payload');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('device_id')->references('device_id')->on('device_registries')->onDelete('cascade');
        });

        Schema::create('activity_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('device_id');
            $table->jsonb('data');
            $table->timestamps();
        });

        Schema::create('conflict_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('device_id');
            $table->jsonb('data');
            $table->timestamps();
        });

        Schema::create('returns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('device_id');
            $table->jsonb('data');
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('device_id');
            $table->jsonb('data');
            $table->timestamps();
        });

    }

    public function down(): void {
        Schema::dropIfExists('sync_queues');
        Schema::dropIfExists('device_registries');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('ledgers');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('domains');
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
        });
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('activity_history');
        Schema::dropIfExists('conflict_logs');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('shipments');
    }
};
