<?php

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class OrderViewService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function find(int $id): ?Order
    {
        return $this->orderRepository->findByIdWithItems($id);
    }
}
