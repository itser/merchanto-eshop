<?php

namespace Modules\Catalog\Repositories\Eloquent;

use App\Repositories\Eloquent\EloquentRepository;
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
