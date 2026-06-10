<?php

namespace Modules\Order\Services;

use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class OrderManagementService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Order $order, array $attributes): Order
    {
        if (! array_key_exists('status', $attributes)) {
            return $order;
        }

        $status = $attributes['status'];

        if (is_string($status)) {
            $status = OrderStatus::from($status);
        }

        return $this->orderRepository->updateStatus($order, $status);
    }
}
