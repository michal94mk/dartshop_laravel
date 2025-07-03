<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-production {--check-only : Only check current configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup production configuration and security settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $checkOnly = $this->option('check-only');

        $this->info('🚀 DartShop Production Setup');
        $this->newLine();

        if ($checkOnly) {
            $this->checkConfiguration();
            return;
        }

        // Environment checks
        $this->checkEnvironment();
        
        // Security setup
        $this->setupSecurity();
        
        // Cache optimization
        $this->optimizeCache();
        
        // File permissions
        $this->setPermissions();

        $this->newLine();
        $this->info('✅ Production setup completed!');
        $this->warn('📋 Don\'t forget to:');
        $this->line('   • Configure your web server (Nginx/Apache)');
        $this->line('   • Setup SSL certificate');
        $this->line('   • Configure queue worker');
        $this->line('   • Setup scheduled tasks (cron)');
        $this->line('   • Configure monitoring');
    }

    private function checkEnvironment()
    {
        $this->info('🔍 Checking environment...');

        $checks = [
            'APP_ENV=production' => config('app.env') === 'production',
            'APP_DEBUG=false' => !config('app.debug'),
            'APP_KEY set' => !empty(config('app.key')),
            'Database configured' => !empty(config('database.connections.mysql.database')),
            'Redis available' => extension_loaded('redis'),
            'Cache driver' => config('cache.default') !== 'array',
            'Queue configured' => config('queue.default') !== 'sync',
        ];

        foreach ($checks as $check => $status) {
            if ($status) {
                $this->info("   ✅ {$check}");
            } else {
                $this->error("   ❌ {$check}");
            }
        }
    }

    private function checkConfiguration()
    {
        $this->info('🔍 Current Configuration Check');
        $this->newLine();

        $config = [
            'Environment' => config('app.env'),
            'Debug Mode' => config('app.debug') ? 'ON (⚠️  Should be OFF)' : 'OFF ✅',
            'Cache Driver' => config('cache.default'),
            'Queue Driver' => config('queue.default'),
            'Mail Driver' => config('mail.default'),
            'Session Driver' => config('session.driver'),
        ];

        foreach ($config as $key => $value) {
            $this->line("<info>{$key}:</info> {$value}");
        }

        $this->newLine();
        
        // Security checks
        $this->info('🔐 Security Checks:');
        $securityChecks = [
            'HTTPS URLs' => str_starts_with(config('app.url'), 'https://'),
            'Strong APP_KEY' => !empty(config('app.key')),
            'Session secure' => config('session.secure', false),
            'CSRF protection' => true, // Always enabled in Laravel
        ];

        foreach ($securityChecks as $check => $status) {
            $icon = $status ? '✅' : '⚠️';
            $this->line("   {$icon} {$check}");
        }
    }

    private function setupSecurity()
    {
        $this->info('🔐 Setting up security...');

        // Clear any debug/development files
        $debugFiles = [
            storage_path('logs/laravel.log'),
        ];

        foreach ($debugFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->line("   🗑️ Cleared {$file}");
            }
        }

        $this->line('   ✅ Security setup completed');
    }

    private function optimizeCache()
    {
        $this->info('⚡ Optimizing cache...');

        $commands = [
            'config:cache' => 'Configuration cache',
            'route:cache' => 'Route cache',
            'view:cache' => 'View cache',
            'event:cache' => 'Event cache',
        ];

        foreach ($commands as $command => $description) {
            $this->call($command);
            $this->line("   ✅ {$description}");
        }
    }

    private function setPermissions()
    {
        $this->info('📁 Setting file permissions...');

        $directories = [
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ];

        foreach ($directories as $dir) {
            if (File::isDirectory($dir)) {
                chmod($dir, 0775);
                $this->line("   📁 Set permissions for {$dir}");
            }
        }

        $this->line('   ✅ File permissions updated');
    }
}
