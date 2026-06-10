<?php

namespace Modules\Catalog\Services;

use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class ProductManagementService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Product
    {
        return $this->productRepository->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Product $product, array $attributes): Product
    {
        return $this->productRepository->update($product, $attributes);
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}
