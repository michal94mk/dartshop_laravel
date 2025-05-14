<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Phil Taylor Power 9Five Gen 8',
                'description' => 'The latest generation of Phil Taylor\'s signature darts with Swiss points and 95% tungsten barrels.',
                'price' => 129.99,
                'category_id' => 1, // Darts
                'brand_id' => 3, // Target
                'image' => 'power9five.jpg'
            ],
            [
                'name' => 'Winmau Blade 6 Triple Core',
                'description' => 'The revolutionary triple core dartboard with reduced bounce-outs and improved durability.',
                'price' => 79.99,
                'category_id' => 2, // Dartboards
                'brand_id' => 4, // Winmau
                'image' => 'blade6.jpg'
            ],
            [
                'name' => 'Red Dragon Razor Edge ZX-3',
                'description' => 'Precision-engineered steel-tip darts with 90% tungsten barrels and Nitrotech shafts.',
                'price' => 69.99,
                'category_id' => 1, // Darts
                'brand_id' => 5, // Red Dragon
                'image' => 'razeredge.jpg'
            ],
            [
                'name' => 'Unicorn Eclipse HD2 Pro Dartboard',
                'description' => 'Tournament-quality dartboard with high-definition number ring and ultra-thin wiring.',
                'price' => 64.99,
                'category_id' => 2, // Dartboards
                'brand_id' => 1, // Unicorn
                'image' => 'eclipsehd2.jpg'
            ],
            [
                'name' => 'Harrows Ace Case',
                'description' => 'Premium dart case with secure storage for up to three sets of darts and accessories.',
                'price' => 19.99,
                'category_id' => 5, // Cases
                'brand_id' => 2, // Harrows
                'image' => 'acecase.jpg'
            ],
            [
                'name' => 'Target Vision Pro Ultra Flights',
                'description' => 'Aerodynamically engineered flights for improved stability and reduced drag.',
                'price' => 4.99,
                'category_id' => 6, // Flights
                'brand_id' => 3, // Target
                'image' => 'visionpro.jpg'
            ],
            [
                'name' => 'Shot Tribal Weapon Series',
                'description' => 'Distinctive darts with unique tribal engravings and 90% tungsten construction.',
                'price' => 89.99,
                'category_id' => 1, // Darts
                'brand_id' => 6, // Shot Darts
                'image' => 'tribalweapon.jpg'
            ],
            [
                'name' => 'Mission Quad Lock Shafts',
                'description' => 'Revolutionary shaft design with four-point locking system to prevent loosening.',
                'price' => 5.99,
                'category_id' => 7, // Shafts
                'brand_id' => 7, // Mission
                'image' => 'quadlock.jpg'
            ],
            [
                'name' => 'Bull\'s Scorer Dartboard Cabinet',
                'description' => 'Elegant wooden cabinet with scoreboard and storage for darts and accessories.',
                'price' => 109.99,
                'category_id' => 10, // Cabinet Sets
                'brand_id' => 8, // Bull's
                'image' => 'scorer.jpg'
            ],
            [
                'name' => 'Designa Dark Thunder Darts',
                'description' => 'Budget-friendly 90% tungsten darts with black titanium coating.',
                'price' => 24.99,
                'category_id' => 1, // Darts
                'brand_id' => 9, // Designa
                'image' => 'darkthunder.jpg'
            ],
            [
                'name' => 'One80 Gladiator 3 Darts',
                'description' => 'Professional-grade darts with revolutionary grip pattern and 95% tungsten barrels.',
                'price' => 94.99,
                'category_id' => 1, // Darts
                'brand_id' => 10, // One80
                'image' => 'gladiator3.jpg'
            ],
            [
                'name' => 'Target Corona LED Lighting System',
                'description' => 'Professional dartboard lighting system eliminating shadows for better visibility.',
                'price' => 59.99,
                'category_id' => 3, // Accessories
                'brand_id' => 3, // Target
                'image' => 'corona.jpg'
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
} 