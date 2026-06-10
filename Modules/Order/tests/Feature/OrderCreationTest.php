<?php

use App\Exceptions\Catalog\InsufficientStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Services\PlaceOrderService;

uses(RefreshDatabase::class);

test('place order creates order with items snapshot and pending status', function () {
    $product = Product::factory()->create([
        'name' => 'Widget',
        'price' => '25.00',
        'stock' => 10,
    ]);

    $order = app(PlaceOrderService::class)->place(
        customerName: 'Jane Doe',
        customerEmail: 'jane@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    );

    expect($order->customer_name)->toBe('Jane Doe')
        ->and($order->customer_email)->toBe('jane@example.com')
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->total)->toBe('50.00')
        ->and($order->items)->toHaveCount(1);

    $item = $order->items->first();

    expect($item->product_id)->toBe($product->id)
        ->and($item->product_name)->toBe('Widget')
        ->and($item->product_price)->toBe('25.00')
        ->and($item->quantity)->toBe(2);
});

test('place order decrements product stock via catalog contract', function () {
    $product = Product::factory()->create([
        'price' => '10.00',
        'stock' => 10,
    ]);

    app(PlaceOrderService::class)->place(
        customerName: 'John Doe',
        customerEmail: 'john@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 3],
        ],
    );

    expect($product->fresh()->stock)->toBe(7);
});

test('place order calculates total for multiple line items', function () {
    $first = Product::factory()->create(['price' => '10.00', 'stock' => 10]);
    $second = Product::factory()->create(['price' => '5.50', 'stock' => 10]);

    $order = app(PlaceOrderService::class)->place(
        customerName: 'Alex Smith',
        customerEmail: 'alex@example.com',
        items: [
            ['product_id' => $first->id, 'quantity' => 2],
            ['product_id' => $second->id, 'quantity' => 1],
        ],
    );

    expect($order->total)->toBe('25.50')
        ->and($order->items)->toHaveCount(2);
});

test('place order throws when stock is insufficient', function () {
    $product = Product::factory()->create(['stock' => 2]);

    app(PlaceOrderService::class)->place(
        customerName: 'John Doe',
        customerEmail: 'john@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 3],
        ],
    );
})->throws(InsufficientStockException::class);

test('failed place order does not persist order or decrement stock', function () {
    $product = Product::factory()->create(['stock' => 2]);

    expect(fn () => app(PlaceOrderService::class)->place(
        customerName: 'John Doe',
        customerEmail: 'john@example.com',
        items: [
            ['product_id' => $product->id, 'quantity' => 3],
        ],
    ))->toThrow(InsufficientStockException::class);

    expect(Order::query()->count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(2);
});
