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
                'token' => null,
                'is_active' => true,
                'verified_at' => now(),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'email' => 'test2@example.com',
                'token' => 'test_token_123',
                'is_active' => false,
                'verified_at' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'email' => 'test3@example.com',
                'token' => null,
                'is_active' => true,
                'verified_at' => now()->subDays(3),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(3),
            ],
            [
                'email' => 'test4@example.com',
                'token' => null,
                'is_active' => false,
                'verified_at' => now()->subDays(15),
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(2),
            ],
            [
                'email' => 'test5@example.com',
                'token' => null,
                'is_active' => true,
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