<?php

namespace Modules\Catalog\Repositories\Eloquent;

use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class EloquentProductRepository extends EloquentRepository implements ProductRepositoryInterface
{
    public function listForStorefront(): Collection
    {
        return Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    protected function modelClass(): string
    {
        return Product::class;
    }
}
