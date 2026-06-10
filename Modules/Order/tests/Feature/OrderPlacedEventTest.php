<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Catalog\Models\Product;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Listeners\ClearCartOnOrderPlaced;
use Modules\Order\Models\Order;
use Modules\Order\Services\CartService;
use Modules\Order\Services\PlaceOrderService;

uses(RefreshDatabase::class);

beforeEach(function () {
    session()->start();
    app(CartService::class)->clear();
});

test('place order dispatches order placed event', function () {
    Event::fake([OrderPlaced::class]);

    $product = Product::factory()->create(['stock' => 5]);

    app(PlaceOrderService::class)->place(
        customerName: 'Jane Doe',
        customerEmail: 'jane@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    );

    Event::assertDispatched(OrderPlaced::class, function (OrderPlaced $event): bool {
        return $event->order->customer_email === 'jane@example.com';
    });
});

test('failed place order does not dispatch order placed event', function () {
    Event::fake([OrderPlaced::class]);

    $product = Product::factory()->create(['stock' => 1]);

    expect(fn () => app(PlaceOrderService::class)->place(
        customerName: 'Jane Doe',
        customerEmail: 'jane@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 3],
        ],
    ))->toThrow(\App\Exceptions\Catalog\InsufficientStockException::class);

    Event::assertNotDispatched(OrderPlaced::class);
});

test('clear cart listener empties session cart on order placed', function () {
    app(CartService::class)->add(productId: 1, quantity: 2);

    $order = Order::factory()->create();

    app(ClearCartOnOrderPlaced::class)->handle(new OrderPlaced($order));

    expect(app(CartService::class)->isEmpty())->toBeTrue();
});

test('placing order clears cart via order placed event', function () {
    $product = Product::factory()->create(['stock' => 5]);

    app(CartService::class)->add($product->id, 2);

    app(PlaceOrderService::class)->place(
        customerName: 'Jane Doe',
        customerEmail: 'jane@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    );

    expect(app(CartService::class)->isEmpty())->toBeTrue();
});
