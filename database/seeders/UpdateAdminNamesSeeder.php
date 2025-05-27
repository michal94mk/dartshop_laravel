<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateAdminNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find admin users who don't have first_name or last_name filled
        $admins = User::where('is_admin', true)
            ->where(function($query) {
                $query->whereNull('first_name')
                      ->orWhereNull('last_name')
                      ->orWhere('first_name', '')
                      ->orWhere('last_name', '');
            })
            ->get();

        foreach ($admins as $admin) {
            // If first_name or last_name is empty, try to parse from name
            if (empty($admin->first_name) || empty($admin->last_name)) {
                $nameParts = explode(' ', trim($admin->name));
                
                if (count($nameParts) >= 2) {
                    $admin->first_name = $nameParts[0];
                    $admin->last_name = implode(' ', array_slice($nameParts, 1));
                } else {
                    // If name has only one part, use it as first_name
                    $admin->first_name = $admin->name;
                    $admin->last_name = 'Admin';
                }
                
                $admin->save();
                
                $this->command->info("Updated admin user: {$admin->email} - {$admin->first_name} {$admin->last_name}");
            }
        }
        
        if ($admins->count() === 0) {
            $this->command->info("All admin users already have first_name and last_name filled.");
        }
    }
} 