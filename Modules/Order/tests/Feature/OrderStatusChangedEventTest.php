<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Events\OrderStatusChanged;
use Modules\Order\Exceptions\InvalidOrderStatusTransitionException;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderManagementService;

uses(RefreshDatabase::class);

test('order status update dispatches order status changed event', function () {
    Event::fake([OrderStatusChanged::class]);

    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    app(OrderManagementService::class)->update($order, [
        'status' => OrderStatus::Confirmed,
    ]);

    Event::assertDispatched(OrderStatusChanged::class, function (OrderStatusChanged $event) use ($order): bool {
        return $event->order->is($order)
            && $event->previousStatus === OrderStatus::Pending
            && $event->newStatus === OrderStatus::Confirmed;
    });
});

test('order status update with same status does not dispatch event', function () {
    Event::fake([OrderStatusChanged::class]);

    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    app(OrderManagementService::class)->update($order, [
        'status' => OrderStatus::Pending,
    ]);

    Event::assertNotDispatched(OrderStatusChanged::class);
});

test('order update without status does not dispatch event', function () {
    Event::fake([OrderStatusChanged::class]);

    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    app(OrderManagementService::class)->update($order, []);

    Event::assertNotDispatched(OrderStatusChanged::class);
});

test('failed order status update does not dispatch event', function () {
    Event::fake([OrderStatusChanged::class]);

    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    expect(fn () => app(OrderManagementService::class)->update($order, [
        'status' => OrderStatus::Shipped,
    ]))->toThrow(InvalidOrderStatusTransitionException::class);

    Event::assertNotDispatched(OrderStatusChanged::class);
});
