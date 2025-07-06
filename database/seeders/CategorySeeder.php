<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Lotki',
                'slug' => 'lotki'
            ],
            [
                'name' => 'Tarcze',
                'slug' => 'tarcze'
            ],
            [
                'name' => 'Opony',
                'slug' => 'opony'
            ],
            [
                'name' => 'Piórka',
                'slug' => 'piorka'
            ],
            [
                'name' => 'Shafty',
                'slug' => 'shafty'
            ],
            [
                'name' => 'Oświetlenie',
                'slug' => 'oswietlenie'
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
    }
} 