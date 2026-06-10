<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\DataTransferObjects\CartLine;
use Modules\Order\Services\CartService;

uses(RefreshDatabase::class);

beforeEach(function () {
    session()->start();
    app(CartService::class)->clear();
});

test('cart starts empty', function () {
    $cart = app(CartService::class);

    expect($cart->isEmpty())->toBeTrue()
        ->and($cart->lines())->toBe([])
        ->and($cart->totalQuantity())->toBe(0);
});

test('cart service adds product to session', function () {
    app(CartService::class)->add(productId: 1, quantity: 2);

    $lines = app(CartService::class)->lines();

    expect($lines)->toHaveCount(1);

    $line = $lines[0];

    expect($line)->toBeInstanceOf(CartLine::class)
        ->and($line->productId)->toBe(1)
        ->and($line->quantity)->toBe(2);
});

test('cart service merges quantity when adding same product again', function () {
    $cart = app(CartService::class);

    $cart->add(productId: 1, quantity: 2);
    $cart->add(productId: 1, quantity: 3);

    expect($cart->lines())->toHaveCount(1)
        ->and($cart->lines()[0]->quantity)->toBe(5)
        ->and($cart->totalQuantity())->toBe(5);
});

test('cart service stores multiple products', function () {
    $cart = app(CartService::class);

    $cart->add(productId: 1, quantity: 1);
    $cart->add(productId: 2, quantity: 2);

    expect($cart->lines())->toHaveCount(2)
        ->and($cart->totalQuantity())->toBe(3);
});

test('cart service removes product from session', function () {
    $cart = app(CartService::class);

    $cart->add(productId: 1, quantity: 2);
    $cart->add(productId: 2, quantity: 1);
    $cart->remove(productId: 1);

    expect($cart->lines())->toHaveCount(1)
        ->and($cart->lines()[0]->productId)->toBe(2);
});

test('cart service clears all items', function () {
    $cart = app(CartService::class);

    $cart->add(productId: 1, quantity: 2);
    $cart->clear();

    expect($cart->isEmpty())->toBeTrue()
        ->and($cart->totalQuantity())->toBe(0);
});

test('cart persists in session between requests', function () {
    app(CartService::class)->add(productId: 5, quantity: 1);

    session()->save();

    expect(app(CartService::class)->totalQuantity())->toBe(1);
});
