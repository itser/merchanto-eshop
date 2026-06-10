<?php

namespace App\DataTransferObjects\Catalog;

readonly class ProductData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $price,
        public int $stock,
    ) {}
}
