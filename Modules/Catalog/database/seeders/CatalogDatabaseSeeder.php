<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
