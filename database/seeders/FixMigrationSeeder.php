<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert the missing migration record
        DB::table('migrations')->insert([
            'migration' => '2025_05_23_082856_create_user_favorite_products_table',
            'batch' => 1,
        ]);
        
        $this->command->info('Fixed migration record for user_favorite_products table.');
    }
} 