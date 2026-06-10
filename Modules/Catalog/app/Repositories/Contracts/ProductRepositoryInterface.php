<?php

namespace Modules\Catalog\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\Models\Product;

interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Builder<Product>
     */
    public function query(): Builder;

    /**
     * @return Collection<int, Product>
     */
    public function listForStorefront(): Collection;

    /**
     * @return Collection<int, Product>
     */
    public function listAvailable(): Collection;

    public function findById(int $id): ?Product;

    public function hasStock(int $productId, int $quantity): bool;

    public function decrementStock(int $productId, int $quantity): void;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Product;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Product $product, array $attributes): Product;

    public function delete(Product $product): void;
}
