<?php

namespace Modules\Order\Repositories\Eloquent;

use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class EloquentOrderRepository extends EloquentRepository implements OrderRepositoryInterface
{
    public function query(): Builder
    {
        return Order::query()->latest();
    }

    public function findByIdWithItems(int $id): ?Order
    {
        return Order::query()->with('items')->find($id);
    }

    public function createWithItems(array $orderAttributes, array $itemsAttributes): Order
    {
        $order = Order::query()->create($orderAttributes);

        foreach ($itemsAttributes as $itemAttributes) {
            $order->items()->create($itemAttributes);
        }

        return $order->load('items');
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $order->update(['status' => $status]);

        return $order;
    }

    public function listForAdmin(): Collection
    {
        return Order::query()
            ->with('items')
            ->latest()
            ->get();
    }

    protected function modelClass(): string
    {
        return Order::class;
    }
}
