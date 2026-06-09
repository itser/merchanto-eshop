<?php

use Database\Seeders\AdminSeeder;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(AdminSeeder::class);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('guest can view admin login page', function () {
    get('/admin/login')->assertOk();
});

test('admin can log in through filament login page', function () {
    Livewire::test(Login::class)
        ->fillForm([
            'email' => config('admin.email'),
            'password' => config('admin.password'),
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');
});
