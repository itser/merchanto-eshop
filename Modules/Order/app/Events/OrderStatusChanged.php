<?php

namespace Modules\Order\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;

class OrderStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly OrderStatus $previousStatus,
        public readonly OrderStatus $newStatus,
    ) {}
}
