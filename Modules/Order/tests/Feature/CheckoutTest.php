<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;
use Modules\Order\Models\Order;
use Modules\Order\Services\CartService;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function () {
    session()->start();
    app(CartService::class)->clear();
});

test('guest is redirected from checkout when cart is empty', function () {
    get(route('checkout.index'))
        ->assertRedirect(route('products.index'));
});

test('guest can view checkout page with cart items', function () {
    $product = Product::factory()->create([
        'name' => 'Checkout Widget',
        'price' => '19.99',
        'stock' => 5,
    ]);

    app(CartService::class)->add($product->id, 2);

    get(route('checkout.index'))
        ->assertOk()
        ->assertSee('Checkout')
        ->assertSee('Checkout Widget')
        ->assertSee('19.99')
        ->assertSee('39.98');
});

test('guest can place order from checkout and cart is cleared', function () {
    $product = Product::factory()->create([
        'name' => 'Order Widget',
        'price' => '10.00',
        'stock' => 5,
    ]);

    app(CartService::class)->add($product->id, 2);

    post(route('checkout.store'), [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
    ])
        ->assertRedirect();

    $order = Order::query()->first();

    expect($order)->not->toBeNull()
        ->and($order->customer_name)->toBe('Jane Doe')
        ->and($order->customer_email)->toBe('jane@example.com')
        ->and($order->total)->toBe('20.00')
        ->and(app(CartService::class)->isEmpty())->toBeTrue();

    assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => 'Order Widget',
        'product_price' => '10.00',
        'quantity' => 2,
    ]);
});

test('checkout requires customer name and email', function () {
    $product = Product::factory()->create(['stock' => 5]);
    app(CartService::class)->add($product->id, 1);

    post(route('checkout.store'), [])
        ->assertSessionHasErrors(['customer_name', 'customer_email']);
});

test('checkout rejects empty cart submission', function () {
    post(route('checkout.store'), [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
    ])
        ->assertRedirect(route('products.index'));

    expect(Order::query()->count())->toBe(0);
});

test('checkout does not create order when stock is insufficient', function () {
    $product = Product::factory()->create(['stock' => 1]);
    app(CartService::class)->add($product->id, 3);

    post(route('checkout.store'), [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
    ])
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHasErrors('checkout');

    expect(Order::query()->count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(1)
        ->and(app(CartService::class)->totalQuantity())->toBe(3);
});
