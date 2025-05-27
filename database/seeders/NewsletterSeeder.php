<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NewsletterSubscription;

class NewsletterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'email' => 'test1@example.com',
                'status' => 'active',
                'source' => 'footer',
                'verified_at' => now(),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'email' => 'test2@example.com',
                'status' => 'pending',
                'source' => 'popup',
                'verification_token' => 'test_token_123',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'email' => 'test3@example.com',
                'status' => 'active',
                'source' => 'website',
                'verified_at' => now()->subDays(3),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(3),
            ],
            [
                'email' => 'test4@example.com',
                'status' => 'unsubscribed',
                'source' => 'footer',
                'verified_at' => now()->subDays(15),
                'unsubscribed_at' => now()->subDays(2),
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(2),
            ],
            [
                'email' => 'test5@example.com',
                'status' => 'active',
                'source' => 'popup',
                'verified_at' => now()->subDays(1),
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(1),
            ],
        ];

        foreach ($subscriptions as $subscription) {
            NewsletterSubscription::create($subscription);
        }
    }
} 