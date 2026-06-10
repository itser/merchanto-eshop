<?php

namespace Modules\Catalog\Repositories\Eloquent;

use App\Repositories\Eloquent\EloquentRepository;
use App\Exceptions\Catalog\InsufficientStockException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class EloquentProductRepository extends EloquentRepository implements ProductRepositoryInterface
{
    public function query(): Builder
    {
        return Product::query()
            ->with('category')
            ->orderBy('name');
    }

    public function listForStorefront(): Collection
    {
        return Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    public function listAvailable(): Collection
    {
        return Product::query()
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?Product
    {
        return Product::query()->find($id);
    }

    public function hasStock(int $productId, int $quantity): bool
    {
        $product = $this->findById($productId);

        return $product !== null && $product->stock >= $quantity;
    }

    public function decrementStock(int $productId, int $quantity): void
    {
        $affected = Product::query()
            ->whereKey($productId)
            ->where('stock', '>=', $quantity)
            ->decrement('stock', $quantity);

        if ($affected === 0) {
            throw new InsufficientStockException($productId, $quantity);
        }
    }

    public function create(array $attributes): Product
    {
        return Product::query()->create($attributes);
    }

    public function update(Product $product, array $attributes): Product
    {
        $product->update($attributes);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    protected function modelClass(): string
    {
        return Product::class;
    }
}
