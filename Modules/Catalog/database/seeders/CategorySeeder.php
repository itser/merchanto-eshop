<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $categories = [
        'Electronics',
        'Books',
        'Home & Garden',
    ];

    public function run(): void
    {
        foreach ($this->categories as $name) {
            Category::query()->updateOrCreate(['name' => $name]);
        }
    }
}
