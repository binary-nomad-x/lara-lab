<?php
$files = glob(__DIR__ . '/database/migrations/2026_03_26_*.php');
foreach ($files as $file) {
    unlink($file);
    echo "Deleted " . basename($file) . "\n";
}
