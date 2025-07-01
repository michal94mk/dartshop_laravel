<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing messages
        ContactMessage::truncate();
        
        // Sample data without faker
        $names = ['Jan Kowalski', 'Anna Nowak', 'Piotr Wiśniewski', 'Maria Wójcik', 'Tomasz Kowalczyk'];
        $emails = ['jan@email.com', 'anna@email.com', 'piotr@email.com', 'maria@email.com', 'tomasz@email.com'];
        $messages = [
            'Szukam informacji o dostępności produktu. Czy można zamówić?',
            'Mam problem z zamówieniem. Kiedy zostanie wysłane?',
            'Chciałbym zwrócić produkt. Jaka jest procedura?',
            'Potrzebuję więcej informacji o produkcie.',
            'Świetna obsługa! Dziękuję za szybką realizację.'
        ];
        
        // Common subjects
        $subjects = [
            'Product inquiry',
            'Order issue',
            'Product return',
            'Product availability',
            'Information request',
            'Complaint',
            'Shipping question',
            'Payment',
            'Cooperation',
            'Feedback'
        ];
        
        // Create different types of messages
        
        // Type 1: Unread messages (newest)
        for ($i = 0; $i < 5; $i++) {
            ContactMessage::create([
                'name' => $names[$i],
                'email' => $emails[$i],
                'subject' => $subjects[array_rand($subjects)] . ' - ' . ($i + 1),
                'message' => $messages[$i],
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subDays($i + 1),
                'updated_at' => now()->subDays($i + 1),
            ]);
        }
        
        // Type 2: Read messages
        for ($i = 0; $i < 5; $i++) {
            $createdAt = now()->subDays(rand(10, 30));
            $readAt = $createdAt->copy()->addDays(rand(1, 5));
            
            ContactMessage::create([
                'name' => $names[$i],
                'email' => $emails[$i],
                'subject' => $subjects[array_rand($subjects)] . ' - Read ' . ($i + 1),
                'message' => $messages[$i] . "\n\nPozdrawiam,\n" . explode(' ', $names[$i])[0],
                'is_read' => true,
                'read_at' => $readAt,
                'created_at' => $createdAt,
                'updated_at' => $readAt,
            ]);
        }
        
        // Type 3: Some older messages (mix of read/unread)
        for ($i = 0; $i < 5; $i++) {
            $createdAt = now()->subDays(rand(30, 90));
            $isRead = $i % 2 == 0; // Every other message is read
            $readAt = $isRead ? $createdAt->copy()->addDays(rand(1, 10)) : null;
            
            ContactMessage::create([
                'name' => $names[$i],
                'email' => $emails[$i],
                'subject' => $subjects[array_rand($subjects)] . ' - Old ' . ($i + 1),
                'message' => $messages[$i],
                'is_read' => $isRead,
                'read_at' => $readAt,
                'created_at' => $createdAt,
                'updated_at' => $readAt ?? $createdAt,
            ]);
        }
        
        $this->command->info('20 contact messages created successfully!');
    }
}
