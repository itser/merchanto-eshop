<?php

namespace App\DataTransferObjects\Catalog;

readonly class ProductSnapshot
{
    public function __construct(
        public int $productId,
        public string $name,
        public string $price,
        public int $quantity,
    ) {}
}
