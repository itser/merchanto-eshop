<?php

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\CreateProduct;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\EditProduct;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\ListProducts;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(AdminSeeder::class);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    actingAs(User::query()->where('email', config('admin.email'))->firstOrFail());
});

test('admin can list products in filament', function () {
    $products = Product::factory()->count(3)->create();

    Livewire::test(ListProducts::class)
        ->assertCanSeeTableRecords($products);
});

test('admin can create a product', function () {
    $category = Category::factory()->create(['name' => 'Electronics']);

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $category->id,
            'name' => 'Wireless Mouse',
            'description' => 'Ergonomic wireless mouse',
            'price' => '29.99',
            'stock' => 50,
        ])
        ->call('create')
        ->assertNotified()
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('products', [
        'category_id' => $category->id,
        'name' => 'Wireless Mouse',
        'description' => 'Ergonomic wireless mouse',
        'price' => '29.99',
        'stock' => 50,
    ]);
});

test('admin can update a product', function () {
    $product = Product::factory()->create([
        'name' => 'Old Keyboard',
        'price' => '10.00',
        'stock' => 5,
    ]);

    Livewire::test(EditProduct::class, ['record' => $product->id])
        ->fillForm([
            'name' => 'Mechanical Keyboard',
            'price' => '89.50',
            'stock' => 12,
        ])
        ->call('save')
        ->assertNotified()
        ->assertHasNoFormErrors();

    assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Mechanical Keyboard',
        'price' => '89.50',
        'stock' => 12,
    ]);
});

test('admin can delete a product', function () {
    $product = Product::factory()->create();

    Livewire::test(EditProduct::class, ['record' => $product->id])
        ->callAction(DeleteAction::class)
        ->assertNotified();

    assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
