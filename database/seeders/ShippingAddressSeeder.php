<?php

namespace Database\Seeders;

use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users to attach shipping addresses to
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Run UserSeeder first.');
            return;
        }
        
        // Sample Polish cities and addresses
        $cities = ['Warszawa', 'Kraków', 'Wrocław', 'Poznań', 'Gdańsk', 'Szczecin', 'Łódź', 'Katowice'];
        $streets = ['ul. Mickiewicza', 'ul. Słowackiego', 'ul. Kościuszki', 'ul. Piłsudskiego', 'ul. Sienkiewicza', 'ul. Warszawska'];
        
        foreach ($users as $user) {
            // Create 1-3 addresses for each user
            $addressCount = rand(1, 3);
            
            for ($i = 0; $i < $addressCount; $i++) {
                $isDefault = ($i === 0); // Make first address default
                
                ShippingAddress::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'address' => $streets[array_rand($streets)] . ' ' . rand(1, 100),
                    'city' => $cities[array_rand($cities)],
                    'state' => rand(0, 1) ? 'Województwo ' . $cities[array_rand($cities)] : null,
                    'postal_code' => rand(10, 99) . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'country' => 'Polska',
                    'phone' => '+48 ' . rand(500, 899) . ' ' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                    'is_default' => $isDefault
                ]);
            }
        }
        
        $this->command->info('Shipping addresses created successfully!');
    }
}
