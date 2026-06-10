<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;

class OrderDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SampleOrderSeeder::class,
        ]);
    }
}
