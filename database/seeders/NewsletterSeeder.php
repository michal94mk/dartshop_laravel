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
                'email' => 'anna.kowalska@example.com',
                'status' => 'active',
                'verification_token' => null,
                'verified_at' => now()->subDays(10),
                'unsubscribed_at' => null,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'email' => 'jan.nowak@example.com',
                'status' => 'pending',
                'verification_token' => 'abc123token456def',
                'verified_at' => null,
                'unsubscribed_at' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'email' => 'maria.wisniowska@example.com',
                'status' => 'active',
                'verification_token' => null,
                'verified_at' => now()->subDays(3),
                'unsubscribed_at' => null,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(3),
            ],
            [
                'email' => 'piotr.kaczmarek@example.com',
                'status' => 'unsubscribed',
                'verification_token' => null,
                'verified_at' => now()->subDays(15),
                'unsubscribed_at' => now()->subDays(2),
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(2),
            ],
            [
                'email' => 'katarzyna.lewandowska@example.com',
                'status' => 'active',
                'verification_token' => null,
                'verified_at' => now()->subDays(1),
                'unsubscribed_at' => null,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(1),
            ],
            [
                'email' => 'tomasz.wojcik@example.com',
                'status' => 'pending',
                'verification_token' => 'xyz789pending123abc',
                'verified_at' => null,
                'unsubscribed_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($subscriptions as $subscription) {
            NewsletterSubscription::create($subscription);
        }
    }
} 