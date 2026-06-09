<?php

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Catalog\Filament\Resources\CategoryResource\Pages\CreateCategory;
use Modules\Catalog\Filament\Resources\CategoryResource\Pages\EditCategory;
use Modules\Catalog\Filament\Resources\CategoryResource\Pages\ListCategories;
use Modules\Catalog\Models\Category;

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

test('admin can list categories in filament', function () {
    $categories = Category::factory()->count(3)->create();

    Livewire::test(ListCategories::class)
        ->assertCanSeeTableRecords($categories);
});

test('admin can create a category', function () {
    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => 'Electronics',
        ])
        ->call('create')
        ->assertNotified()
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('categories', [
        'name' => 'Electronics',
    ]);
});

test('admin can update a category', function () {
    $category = Category::factory()->create(['name' => 'Books']);

    Livewire::test(EditCategory::class, ['record' => $category->id])
        ->fillForm([
            'name' => 'Science Books',
        ])
        ->call('save')
        ->assertNotified()
        ->assertHasNoFormErrors();

    assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Science Books',
    ]);
});

test('admin can delete a category', function () {
    $category = Category::factory()->create();

    Livewire::test(EditCategory::class, ['record' => $category->id])
        ->callAction(DeleteAction::class)
        ->assertNotified();

    assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});
