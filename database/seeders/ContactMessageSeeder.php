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
        
        // Create different types of messages with various statuses
        
        // Type 1: Unread messages (newest)
        for ($i = 0; $i < 5; $i++) {
            ContactMessage::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'subject' => $subjects[array_rand($subjects)] . ' - ' . $faker->word,
                'message' => $faker->paragraph(4, true) . "\n\n" . $faker->paragraph(3, true),
                'status' => 'unread',
                'created_at' => $faker->dateTimeBetween('-1 week', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 week', 'now'),
            ]);
        }
        
        // Type 2: Read messages
        for ($i = 0; $i < 7; $i++) {
            $createdAt = $faker->dateTimeBetween('-2 months', '-2 weeks');
            $updatedAt = Carbon::instance($createdAt)->addDays(rand(1, 5));
            
            ContactMessage::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'subject' => $subjects[array_rand($subjects)] . ' - ' . $faker->word,
                'message' => $faker->paragraph(3, true) . "\n\n" . $faker->paragraph(2, true),
                'notes' => rand(0, 1) ? $faker->sentence(10, true) : null,
                'status' => 'read',
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }
        
        // Type 3: Replied messages
        for ($i = 0; $i < 8; $i++) {
            $createdAt = $faker->dateTimeBetween('-3 months', '-1 month');
            $updatedAt = Carbon::instance($createdAt)->addDays(rand(1, 10));
            $responseDate = Carbon::instance($updatedAt)->format('d.m.Y H:i');
            
            $reply = $faker->paragraph(3, true);
            $notes = rand(0, 1) ? 
                "--- Odpowiedź wysłana {$responseDate} ---\n{$reply}\n\n" . $faker->sentence(8, true) : 
                "--- Odpowiedź wysłana {$responseDate} ---\n{$reply}";
            
            ContactMessage::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'subject' => $subjects[array_rand($subjects)] . ' - ' . $faker->word,
                'message' => $faker->paragraph(3, true) . "\n\nPozdrawiam,\n" . $faker->firstName,
                'reply' => $reply,
                'notes' => $notes,
                'status' => 'replied',
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }
        
        $this->command->info('20 contact messages created successfully!');
    }
}
