<?php

namespace Modules\Order\DataTransferObjects;

readonly class CartLine
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}
}
