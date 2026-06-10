<?php

namespace Modules\Order\Services;

use Modules\Order\Enums\OrderStatus;
use Modules\Order\Events\OrderStatusChanged;
use Modules\Order\Exceptions\InvalidOrderStatusTransitionException;
use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class OrderManagementService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws InvalidOrderStatusTransitionException
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

        if (! $order->status->canTransitionTo($status)) {
            throw new InvalidOrderStatusTransitionException($order->status, $status);
        }

        if ($order->status === $status) {
            return $order;
        }

        $previousStatus = $order->status;

        $order = $this->orderRepository->updateStatus($order, $status);

        event(new OrderStatusChanged($order, $previousStatus, $status));

        return $order;
    }
}
