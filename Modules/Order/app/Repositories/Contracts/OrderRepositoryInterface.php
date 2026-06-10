<?php

namespace Modules\Order\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;

interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Builder<Order>
     */
    public function query(): Builder;

    public function findByIdWithItems(int $id): ?Order;

    /**
     * @param  array<string, mixed>  $orderAttributes
     * @param  list<array<string, mixed>>  $itemsAttributes
     */
    public function createWithItems(array $orderAttributes, array $itemsAttributes): Order;

    public function updateStatus(Order $order, OrderStatus $status): Order;

    /**
     * @return Collection<int, Order>
     */
    public function listForAdmin(): Collection;
}
