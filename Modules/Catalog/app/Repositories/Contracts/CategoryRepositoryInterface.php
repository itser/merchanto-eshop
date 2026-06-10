<?php

namespace Modules\Catalog\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\Models\Category;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Builder<Category>
     */
    public function query(): Builder;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Category;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Category $category, array $attributes): Category;

    public function delete(Category $category): void;
}
