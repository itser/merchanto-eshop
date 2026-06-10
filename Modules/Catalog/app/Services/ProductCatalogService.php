<?php

namespace Modules\Catalog\Services;

use App\Contracts\Catalog\ProductCatalogInterface;
use App\DataTransferObjects\Catalog\ProductData;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class ProductCatalogService implements ProductCatalogInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function findById(int $id): ?ProductData
    {
        $product = $this->productRepository->findById($id);

        return $product !== null ? $this->toProductData($product) : null;
    }

    public function listAvailable(): array
    {
        return $this->productRepository->listAvailable()
            ->map(fn (Product $product): ProductData => $this->toProductData($product))
            ->values()
            ->all();
    }

    public function hasStock(int $productId, int $quantity): bool
    {
        return $this->productRepository->hasStock($productId, $quantity);
    }

    public function decrementStock(int $productId, int $quantity): void
    {
        $this->productRepository->decrementStock($productId, $quantity);
    }

    private function toProductData(Product $product): ProductData
    {
        return new ProductData(
            id: $product->id,
            name: $product->name,
            price: (string) $product->price,
            stock: $product->stock,
        );
    }
}
