<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            'Unicorn',
            'Harrows',
            'Target',
            'Winmau',
            'Red Dragon',
            'Shot Darts',
            'Mission',
            'Bull\'s',
            'Designa',
            'One80'
        ];

        foreach ($brands as $brandName) {
            Brand::create([
                'name' => $brandName
            ]);
        }
    }
} 