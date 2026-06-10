<?php

namespace Modules\Order\Exceptions;

use Modules\Order\Enums\OrderStatus;
use RuntimeException;

class InvalidOrderStatusTransitionException extends RuntimeException
{
    public function __construct(
        public readonly OrderStatus $from,
        public readonly OrderStatus $to,
    ) {
        parent::__construct(
            "Invalid order status transition from [{$from->value}] to [{$to->value}].",
        );
    }
}
