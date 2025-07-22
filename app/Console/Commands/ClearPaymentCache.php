<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Payment\PaymentService;

class ClearPaymentCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:clear-cache 
                            {--products : Clear only product cache}
                            {--config : Clear only configuration cache}
                            {--stats : Show cache statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear payment service cache or show cache statistics';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService): int
    {
        if ($this->option('stats')) {
            $this->showCacheStats($paymentService);
            return self::SUCCESS;
        }

        if ($this->option('products')) {
            $paymentService->clearAllProductCache();
            $this->info('Product cache cleared successfully.');
            return self::SUCCESS;
        }

        if ($this->option('config')) {
            $paymentService->clearCache();
            $this->info('Configuration cache cleared successfully.');
            return self::SUCCESS;
        }

        // Clear all cache by default
        $paymentService->clearCache();
        $paymentService->clearAllProductCache();
        
        $this->info('All payment cache cleared successfully.');
        
        return self::SUCCESS;
    }

    /**
     * Show cache statistics
     */
    private function showCacheStats(PaymentService $paymentService): void
    {
        $stats = $paymentService->getCacheStats();
        
        $this->info('Payment Service Cache Statistics:');
        $this->line('');
        
        $this->table(
            ['Cache Item', 'Status'],
            [
                ['Stripe Configuration', $stats['stripe_config_cached'] ? '✅ Cached' : '❌ Not Cached'],
                ['Payment Methods', $stats['payment_methods_cached'] ? '✅ Cached' : '❌ Not Cached'],
                ['Cache Driver', $stats['cache_driver']],
            ]
        );
        
        $this->line('');
        $this->comment('Use --products to clear product cache');
        $this->comment('Use --config to clear configuration cache');
        $this->comment('Use without options to clear all cache');
    }
} 