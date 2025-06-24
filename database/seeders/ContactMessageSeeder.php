<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing contact messages
        ContactMessage::truncate();
        
        $faker = Faker::create('pl_PL');
        
        // Common subjects for contact messages
        $subjects = [
            'Pytanie o produkt',
            'Problem z zamówieniem',
            'Zwrot produktu',
            'Dostępność produktu',
            'Prośba o informację',
            'Reklamacja',
            'Pytanie o wysyłkę',
            'Płatność',
            'Współpraca',
            'Opinia'
        ];
        
        // Create different types of messages
        
        // Type 1: Unread messages (newest)
        for ($i = 0; $i < 5; $i++) {
            ContactMessage::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'subject' => $subjects[array_rand($subjects)] . ' - ' . $faker->word,
                'message' => $faker->paragraph(4, true) . "\n\n" . $faker->paragraph(3, true),
                'is_read' => false,
                'read_at' => null,
                'created_at' => $faker->dateTimeBetween('-1 week', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 week', 'now'),
            ]);
        }
        
        // Type 2: Read messages
        for ($i = 0; $i < 10; $i++) {
            $createdAt = $faker->dateTimeBetween('-2 months', '-2 weeks');
            $readAt = Carbon::instance($createdAt)->addDays(rand(1, 5));
            
            ContactMessage::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'subject' => $subjects[array_rand($subjects)] . ' - ' . $faker->word,
                'message' => $faker->paragraph(3, true) . "\n\n" . $faker->paragraph(2, true) . "\n\nPozdrawiam,\n" . $faker->firstName,
                'is_read' => true,
                'read_at' => $readAt,
                'created_at' => $createdAt,
                'updated_at' => $readAt,
            ]);
        }
        
        // Type 3: Some older messages (mix of read/unread)
        for ($i = 0; $i < 5; $i++) {
            $createdAt = $faker->dateTimeBetween('-3 months', '-1 month');
            $isRead = $faker->boolean(70); // 70% chance of being read
            $readAt = $isRead ? Carbon::instance($createdAt)->addDays(rand(1, 30)) : null;
            
            ContactMessage::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'subject' => $subjects[array_rand($subjects)] . ' - ' . $faker->word,
                'message' => $faker->paragraph(2, true) . "\n\n" . $faker->paragraph(1, true),
                'is_read' => $isRead,
                'read_at' => $readAt,
                'created_at' => $createdAt,
                'updated_at' => $readAt ?? $createdAt,
            ]);
        }
        
        $this->command->info('20 contact messages created successfully!');
    }
}
