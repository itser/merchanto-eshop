<?php

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => fake()->numberBetween(1, 1000),
            'product_name' => fake()->words(3, true),
            'product_price' => fake()->randomFloat(2, 1, 999),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
