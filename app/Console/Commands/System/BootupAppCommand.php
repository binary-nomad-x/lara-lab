<?php

namespace App\Console\Commands\System;

use Illuminate\Console\Command;

class BootupAppCommand extends Command
{
    protected $signature = 'app:boot {--r|reboot : Wipe database and re-seed (Fresh Start)}';
    protected $description = 'Boot the application (Migrate, Cache Clear, Optional Reboot)';

    public function handle(): int
    {
        $startTime = microtime(true);

        // 1. Display the Banner
        $this->displayBanner();

        // 2. Handle the Reboot Scenario
        if ($this->option('reboot')) {
            $this->warn('⚠️  REBOOT MODE ACTIVATED ⚠️');

            // Safety Confirmation
            if (!$this->confirm('💣  This will WIPE the database and re-seed it. Are you sure?')) {
                $this->comment('Operation cancelled by user.');
                return self::SUCCESS;
            }

            $this->newLine();
            $this->info('⏳ Wiping database and re-running migrations...');

            // migrate:fresh is better than db:wipe + migrate.
            // It drops tables, runs migrations, and seeds all in one optimized go.
            $this->call('migrate:fresh', [
                '--seed' => true,
                '--force' => true // Force action without confirmation
            ]);

        } else {
            // 3. Handle Normal Boot Scenario
            $this->info('📦 Checking for pending migrations...');

            $this->call('migrate', [
                '--force' => true // Ensure it runs in production without prompt (if needed)
            ]);
        }

        // 4. Cache Maintenance (Essential for a "Bootup")
        $this->newLine();
        $this->info('🧹 Clearing system caches...');

        // Run multiple cache clears in silence to keep output clean
        $this->callSilent('config:clear');
        $this->callSilent('route:clear');
        $this->callSilent('view:clear');
        $this->callSilent('cache:clear');

        $this->comment('   - Config cleared');
        $this->comment('   - Routes cleared');
        $this->comment('   - Views cleared');

        // 5. Final Success Message
        $duration = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $this->info('✨ ------------------------------------------------');
        $this->info('✅ Application is READY and RUNNING! 🚀');
        $this->info("✨ Total Boot Time: {$duration}s");
        $this->info('✨ ------------------------------------------------');

        return self::SUCCESS;
    }

    /**
     * Displays a nice ASCII Art Logo
     */
    protected function displayBanner(): void
    {
        $this->newLine();
        $this->info('
             ____            _                 _ 
            |  _ \          | |               | |
            | |_) | __ _  __| | ___   ___ __ _| |
            |  _ < / _` |/ _` |/ _ \ / __/ _` | |
            | |_) | (_| | (_| | (_) | (_| (_| | |
            |____/ \__,_|\__,_|\___/ \___\__,_|_|
        ');
        $this->comment('   Automated System Boot Commander');
        $this->newLine();
    }
}