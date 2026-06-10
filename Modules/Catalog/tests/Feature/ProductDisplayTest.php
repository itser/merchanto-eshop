<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('guest can browse products page', function () {
    get('/products')->assertOk();
});

test('products page displays available products', function () {
    $category = Category::factory()->create(['name' => 'Books']);

    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Laravel Guide',
        'description' => 'A comprehensive Laravel book',
        'price' => '49.99',
        'stock' => 10,
    ]);

    get('/products')
        ->assertOk()
        ->assertSee('Laravel Guide')
        ->assertSee('Books')
        ->assertSee('49.99')
        ->assertSee('10');
});

test('products page shows empty state when no products exist', function () {
    get('/products')
        ->assertOk()
        ->assertSee('No products available');
});

test('products page uses shared add to cart component not order module view', function () {
    $view = file_get_contents(module_path('Catalog', 'resources/views/products/index.blade.php'));

    expect($view)->toContain('<x-add-to-cart');
    expect($view)->not->toContain('Modules\\Order');
});
