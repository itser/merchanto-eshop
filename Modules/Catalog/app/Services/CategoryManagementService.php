<?php

namespace Modules\Catalog\Services;

use Modules\Catalog\Models\Category;
use Modules\Catalog\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryManagementService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Category
    {
        return $this->categoryRepository->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Category $category, array $attributes): Category
    {
        return $this->categoryRepository->update($category, $attributes);
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}
