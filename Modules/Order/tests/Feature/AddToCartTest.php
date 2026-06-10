<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Catalog\Models\Product;
use Modules\Order\Livewire\AddToCartButton;
use Modules\Order\Services\CartService;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    session()->start();
    app(CartService::class)->clear();
});

test('products page shows add to cart for in stock products', function () {
    $product = Product::factory()->create([
        'name' => 'In Stock Widget',
        'stock' => 5,
    ]);

    get('/products')
        ->assertOk()
        ->assertSee('In Stock Widget')
        ->assertSee('Add to cart');
});

test('products page shows out of stock label when stock is zero', function () {
    Product::factory()->create([
        'name' => 'Sold Out Widget',
        'stock' => 0,
    ]);

    get('/products')
        ->assertOk()
        ->assertSee('Sold Out Widget')
        ->assertSee('Out of stock')
        ->assertDontSee('Add to cart');
});

test('guest can add product to cart from livewire button', function () {
    $product = Product::factory()->create(['stock' => 5]);

    Livewire::test(AddToCartButton::class, [
        'productId' => $product->id,
        'canAdd' => true,
    ])
        ->call('addToCart')
        ->assertDispatched('cart-updated');

    expect(app(CartService::class)->totalQuantity())->toBe(1);
});

test('add to cart does nothing when product is out of stock', function () {
    $product = Product::factory()->create(['stock' => 0]);

    Livewire::test(AddToCartButton::class, [
        'productId' => $product->id,
        'canAdd' => false,
    ])
        ->call('addToCart')
        ->assertNotDispatched('cart-updated');

    expect(app(CartService::class)->isEmpty())->toBeTrue();
});

test('add to cart rejects when stock is insufficient', function () {
    $product = Product::factory()->create(['stock' => 0]);

    Livewire::test(AddToCartButton::class, [
        'productId' => $product->id,
        'canAdd' => true,
    ])
        ->call('addToCart')
        ->assertNotDispatched('cart-updated');

    expect(app(CartService::class)->isEmpty())->toBeTrue();
});
