<?php

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Exceptions\InvalidOrderStatusTransitionException;
use Modules\Order\Filament\Resources\OrderResource\Pages\EditOrder;
use Modules\Order\Filament\Resources\OrderResource\Pages\ListOrders;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderManagementService;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(AdminSeeder::class);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    actingAs(User::query()->where('email', config('admin.email'))->firstOrFail());
});

test('admin can list orders in filament', function () {
    $orders = Order::factory()->count(3)->create();

    Livewire::test(ListOrders::class)
        ->assertCanSeeTableRecords($orders);
});

test('admin can update order status in filament', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    Livewire::test(EditOrder::class, ['record' => $order->id])
        ->fillForm([
            'status' => OrderStatus::Confirmed->value,
        ])
        ->call('save')
        ->assertNotified()
        ->assertHasNoFormErrors();

    assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'confirmed',
    ]);
});

test('admin can progress order through status workflow', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Confirmed]);

    Livewire::test(EditOrder::class, ['record' => $order->id])
        ->fillForm([
            'status' => OrderStatus::Shipped->value,
        ])
        ->call('save')
        ->assertNotified()
        ->assertHasNoFormErrors();

    assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'shipped',
    ]);
});

test('order management rejects skipping status in workflow', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    expect(fn () => app(OrderManagementService::class)->update($order, [
        'status' => OrderStatus::Shipped,
    ]))->toThrow(InvalidOrderStatusTransitionException::class);

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
});

test('order management rejects backward status transition', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Shipped]);

    expect(fn () => app(OrderManagementService::class)->update($order, [
        'status' => OrderStatus::Confirmed,
    ]))->toThrow(InvalidOrderStatusTransitionException::class);

    expect($order->fresh()->status)->toBe(OrderStatus::Shipped);
});

test('order management rejects transition from delivered status', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Delivered]);

    expect(fn () => app(OrderManagementService::class)->update($order, [
        'status' => OrderStatus::Shipped,
    ]))->toThrow(InvalidOrderStatusTransitionException::class);

    expect($order->fresh()->status)->toBe(OrderStatus::Delivered);
});

test('admin cannot skip order status in filament', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    Livewire::test(EditOrder::class, ['record' => $order->id])
        ->fillForm([
            'status' => OrderStatus::Shipped->value,
        ])
        ->call('save')
        ->assertHasFormErrors(['status']);

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
});
