<?php

namespace App\Contracts\Catalog;

use App\DataTransferObjects\Catalog\ProductData;

interface ProductCatalogInterface
{
    public function findById(int $id): ?ProductData;

    /**
     * @return list<ProductData>
     */
    public function listAvailable(): array;

    public function hasStock(int $productId, int $quantity): bool;

    public function decrementStock(int $productId, int $quantity): void;
}
