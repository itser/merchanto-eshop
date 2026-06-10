<?php

use App\Contracts\Catalog\ProductCatalogInterface;
use App\DataTransferObjects\Catalog\ProductData;
use App\Exceptions\Catalog\InsufficientStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;

uses(RefreshDatabase::class);

test('product catalog contract is bound in the container', function () {
    expect(app(ProductCatalogInterface::class))->toBeInstanceOf(ProductCatalogInterface::class);
});

test('product catalog find returns product data dto', function () {
    $product = Product::factory()->create([
        'name' => 'Test Widget',
        'price' => '19.99',
        'stock' => 10,
    ]);

    $data = app(ProductCatalogInterface::class)->findById($product->id);

    expect($data)->toBeInstanceOf(ProductData::class)
        ->and($data->id)->toBe($product->id)
        ->and($data->name)->toBe('Test Widget')
        ->and($data->price)->toBe('19.99')
        ->and($data->stock)->toBe(10);
});

test('product catalog find returns null when product does not exist', function () {
    $data = app(ProductCatalogInterface::class)->findById(99999);

    expect($data)->toBeNull();
});

test('product catalog lists available products as dtos', function () {
    Product::factory()->count(2)->create(['stock' => 5]);
    Product::factory()->create(['stock' => 0]);

    $products = app(ProductCatalogInterface::class)->listAvailable();

    expect($products)->toHaveCount(2);

    foreach ($products as $data) {
        expect($data)->toBeInstanceOf(ProductData::class);
    }
});

test('product catalog checks stock availability', function () {
    $product = Product::factory()->create(['stock' => 5]);

    $catalog = app(ProductCatalogInterface::class);

    expect($catalog->hasStock($product->id, 3))->toBeTrue()
        ->and($catalog->hasStock($product->id, 5))->toBeTrue()
        ->and($catalog->hasStock($product->id, 6))->toBeFalse();
});

test('product catalog decrements stock', function () {
    $product = Product::factory()->create(['stock' => 10]);

    app(ProductCatalogInterface::class)->decrementStock($product->id, 3);

    expect($product->fresh()->stock)->toBe(7);
});

test('product catalog decrement fails when insufficient stock', function () {
    $product = Product::factory()->create(['stock' => 2]);

    app(ProductCatalogInterface::class)->decrementStock($product->id, 3);
})->throws(InsufficientStockException::class);
