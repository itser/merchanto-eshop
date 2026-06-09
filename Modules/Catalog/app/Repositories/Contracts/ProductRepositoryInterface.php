<?php

namespace Modules\Catalog\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\Models\Product;

interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection<int, Product>
     */
    public function listForStorefront(): Collection;
}
