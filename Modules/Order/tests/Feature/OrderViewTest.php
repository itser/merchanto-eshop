<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Services\PlaceOrderService;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('guest can view placed order from snapshot data', function () {
    $product = Product::factory()->create([
        'name' => 'Snapshot Widget',
        'price' => '12.50',
        'stock' => 10,
    ]);

    $order = app(PlaceOrderService::class)->place(
        customerName: 'Alex Smith',
        customerEmail: 'alex@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    );

    $product->update(['name' => 'Renamed Widget', 'price' => '99.99']);

    get(route('orders.show', $order))
        ->assertOk()
        ->assertSee('Order #'.$order->id)
        ->assertSee('Alex Smith')
        ->assertSee('alex@example.com')
        ->assertSee('Snapshot Widget')
        ->assertSee('12.50')
        ->assertSee('25.00')
        ->assertSee('Pending')
        ->assertDontSee('Renamed Widget')
        ->assertDontSee('99.99');
});

test('order page shows current status label', function () {
    $order = Order::factory()->create([
        'status' => OrderStatus::Shipped,
    ]);

    get(route('orders.show', $order))
        ->assertOk()
        ->assertSee('Shipped');
});

test('missing order returns not found', function () {
    get(route('orders.show', ['order' => 99999]))
        ->assertNotFound();
});
