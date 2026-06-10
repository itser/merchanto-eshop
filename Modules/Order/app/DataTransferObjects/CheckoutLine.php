<?php

namespace Modules\Order\DataTransferObjects;

readonly class CheckoutLine
{
    public function __construct(
        public int $productId,
        public string $productName,
        public string $productPrice,
        public int $quantity,
        public string $lineTotal,
    ) {}
}
