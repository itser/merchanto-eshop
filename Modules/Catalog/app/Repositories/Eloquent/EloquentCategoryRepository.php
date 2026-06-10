<?php

namespace Modules\Catalog\Repositories\Eloquent;

use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Repositories\Contracts\CategoryRepositoryInterface;

class EloquentCategoryRepository extends EloquentRepository implements CategoryRepositoryInterface
{
    public function query(): Builder
    {
        return Category::query()->orderBy('name');
    }

    public function create(array $attributes): Category
    {
        return Category::query()->create($attributes);
    }

    public function update(Category $category, array $attributes): Category
    {
        $category->update($attributes);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    protected function modelClass(): string
    {
        return Category::class;
    }
}
