<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Order\Livewire\CartBadge;
use Modules\Order\Services\CartService;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    session()->start();
    app(CartService::class)->clear();
});

test('products page renders cart badge in header', function () {
    get('/products')
        ->assertOk()
        ->assertSee('data-test="cart-count"', false);
});

test('cart badge shows zero when cart is empty', function () {
    Livewire::test(CartBadge::class)
        ->assertSet('count', 0)
        ->assertSee('data-test="cart-count"', false);
});

test('cart badge shows total quantity from session cart', function () {
    app(CartService::class)->add(productId: 1, quantity: 2);
    app(CartService::class)->add(productId: 2, quantity: 3);

    Livewire::test(CartBadge::class)
        ->assertSet('count', 5);
});

test('cart badge refreshes when cart updated event is dispatched', function () {
    Livewire::test(CartBadge::class)
        ->assertSet('count', 0);

    app(CartService::class)->add(productId: 1, quantity: 2);

    Livewire::test(CartBadge::class)
        ->dispatch('cart-updated')
        ->assertSet('count', 2);
});
