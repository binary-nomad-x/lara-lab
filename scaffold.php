<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = [
    'OrderItem', 'Shipment', 'ReturnModel', 'Ledger', 'JournalEntry', 
    'Account', 'Currency', 'AuditLog', 'ActivityHistory', 
    'SyncQueue', 'ConflictLog', 'DeviceRegistry'
];

foreach ($models as $model) {
    echo "Creating $model...\n";
    Illuminate\Support\Facades\Artisan::call('make:model', [
        'name' => $model,
        '-m' => true
    ]);
    echo Illuminate\Support\Facades\Artisan::output() . "\n";
}
echo "Done!\n";
