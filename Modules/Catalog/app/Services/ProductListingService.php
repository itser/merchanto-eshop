<?php

namespace Modules\Catalog\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class ProductListingService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * @return Collection<int, Product>
     */
    public function listForStorefront(): Collection
    {
        return $this->productRepository->listForStorefront();
    }
}
