<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pl_PL');
        
        // Create 15 sample contact messages
        for ($i = 0; $i < 15; $i++) {
            // Losowy status wiadomości
            $statuses = ['unread', 'read', 'replied'];
            $status = $statuses[array_rand($statuses)];
            
            ContactMessage::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'subject' => $faker->realText(30),
                'message' => $faker->realText(200),
                'reply' => $status === 'replied' ? $faker->realText(150) : null,
                'status' => $status,
                'created_at' => $faker->dateTimeBetween('-3 months', '-1 week')
            ]);
        }
        
        $this->command->info('Contact messages created successfully!');
    }
}
