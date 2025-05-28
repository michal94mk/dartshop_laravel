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
                'description' => 'Profesjonalne lotki do dart - różne wagi i materiały',
                'slug' => 'lotki',
                'sort_order' => 1,
                'image' => 'lotki.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Tarcze',
                'description' => 'Tarcze do dart - elektroniczne i klasyczne z naturalnej sizalu',
                'slug' => 'tarcze',
                'sort_order' => 2,
                'image' => 'tarcze.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Akcesoria',
                'description' => 'Różnorodne akcesoria do gry w dart - od podstawowych po profesjonalne',
                'slug' => 'akcesoria',
                'sort_order' => 3,
                'image' => 'akcesoria.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Odzież',
                'description' => 'Koszulki, bluzy i inne elementy odzieży dla miłośników dart',
                'slug' => 'odziez',
                'sort_order' => 4,
                'image' => 'odziez.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Walizki i Etui',
                'description' => 'Walizki, etui i pokrowce do bezpiecznego transportu sprzętu',
                'slug' => 'walizki-etui',
                'sort_order' => 5,
                'image' => 'walizki.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Lotki Końcówki',
                'description' => 'Końcówki i groty do lotek - różne długości i materiały',
                'slug' => 'lotki-koncowki',
                'sort_order' => 6,
                'image' => 'koncowki.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Szafy i Szafki',
                'description' => 'Szafy i szafki do zawieszenia tarczy oraz przechowywania sprzętu',
                'slug' => 'szafy-szafki',
                'sort_order' => 7,
                'image' => 'szafy.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Oświetlenie',
                'description' => 'Profesjonalne oświetlenie do tarczy - lampy LED i tradycyjne',
                'slug' => 'oswietlenie',
                'sort_order' => 8,
                'image' => 'oswietlenie.jpg',
                'is_active' => true
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