<?php

namespace App\Exceptions\Catalog;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(
        public readonly int $productId,
        public readonly int $requestedQuantity,
    ) {
        parent::__construct(
            "Insufficient stock for product [{$productId}]. Requested: {$requestedQuantity}.",
        );
    }
}
