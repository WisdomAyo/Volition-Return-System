<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class AppInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Keep the --force option for skipping confirmation
    protected $signature = 'app:install {--force : Force run without confirmation, including destructive actions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all necessary setup processes for the application (DB, Seed, Cache, etc.) using Sanctum for API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting VolitionFund Return System Installation (using Sanctum)...');
        $force = $this->option('force');

        // --- Step 1: Database Wipe (Destructive!) ---
        $this->warn('Step 1: Wiping Database...');
        if ($force || App::environment(['local', 'testing']) || $this->confirm('Do you really want to wipe the entire database? This cannot be undone!')) {
            $this->call('db:wipe');
            $this->info('Database wiped successfully!');
        } else {
            $this->error('Database wipe cancelled by user.');
            return 1;
        }

        // --- Step 2: Database Migrations ---
        $this->warn('Step 2: Running Database Migrations...');
        // Ensure Sanctum's migration runs along with others
        $this->call('migrate', ['--force' => true]);
        $this->info('Database tables migrated successfully!');

        // --- Step 3: Database Seeding ---
        $this->warn('Step 3: Seeding Database...');
        $this->call('db:seed', ['--force' => true]);
        $this->info('Database seeded successfully!');

        // --- Step 4: Passport Installation Removed ---
        // $this->warn('Step 4: Installing Passport (OAuth2 Server)...');
        // $this->call('passport:install', ['--force' => true]);
        // $this->info('API Passport installation completed!');
        $this->comment('Step 4: Skipped Passport installation (Using Sanctum).');


        // --- Step 5: Storage Link ---
        $this->warn('Step 5: Creating Storage Link...');
        if (! File::exists(public_path('storage'))) {
            $this->call('storage:link');
            $this->info('Storage link created successfully.');
        } else {
            $this->comment('Storage link already exists.');
        }

        // --- Step 6: Clear Caches ---
        $this->warn('Step 6: Clearing Application Caches...');
        $this->call('optimize:clear');
        $this->info('Application caches cleared.');

        $this->info('------------------------------------------------');
        $this->info('Volition Fund Return System Installation Complete!');
        $this->info('API Authentication configured with Sanctum.');
        $this->info('You can now start adding returns to the funds using the CLI commands.');

        return Command::SUCCESS;
    }
}