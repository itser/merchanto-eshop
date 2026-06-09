<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * @var list<array{category: string, name: string, description: string, price: float, stock: int}>
     */
    private array $products = [
        [
            'category' => 'Electronics',
            'name' => 'Wireless Mouse',
            'description' => 'Ergonomic wireless mouse with long battery life.',
            'price' => 29.99,
            'stock' => 50,
        ],
        [
            'category' => 'Electronics',
            'name' => 'USB-C Hub',
            'description' => '7-in-1 USB-C hub with HDMI and SD card reader.',
            'price' => 45.00,
            'stock' => 30,
        ],
        [
            'category' => 'Books',
            'name' => 'Laravel Guide',
            'description' => 'A practical guide to building applications with Laravel.',
            'price' => 49.99,
            'stock' => 25,
        ],
        [
            'category' => 'Books',
            'name' => 'Clean Code',
            'description' => 'Classic handbook of agile software craftsmanship.',
            'price' => 39.50,
            'stock' => 15,
        ],
        [
            'category' => 'Home & Garden',
            'name' => 'Ceramic Plant Pot',
            'description' => 'Minimalist ceramic pot for indoor plants.',
            'price' => 18.75,
            'stock' => 40,
        ],
        [
            'category' => 'Home & Garden',
            'name' => 'LED Desk Lamp',
            'description' => 'Adjustable warm-white LED lamp for home office.',
            'price' => 34.99,
            'stock' => 20,
        ],
    ];

    public function run(): void
    {
        foreach ($this->products as $product) {
            $category = Category::query()->where('name', $product['category'])->firstOrFail();

            Product::query()->updateOrCreate(
                [
                    'category_id' => $category->id,
                    'name' => $product['name'],
                ],
                [
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                ],
            );
        }
    }
}
