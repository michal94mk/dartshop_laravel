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
                'name' => 'Akcesoria',
                'slug' => 'akcesoria'
            ],
            [
                'name' => 'Odzież',
                'slug' => 'odziez'
            ],
            [
                'name' => 'Walizki i Etui',
                'slug' => 'walizki-etui'
            ],
            [
                'name' => 'Lotki Końcówki',
                'slug' => 'lotki-koncowki'
            ],
            [
                'name' => 'Szafy i Szafki',
                'slug' => 'szafy-szafki'
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