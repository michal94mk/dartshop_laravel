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
            'Darts',
            'Dartboards',
            'Accessories',
            'Apparel',
            'Cases',
            'Flights',
            'Shafts',
            'Tips',
            'Boards Surrounds',
            'Cabinet Sets'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName)
            ]);
        }
    }
} 