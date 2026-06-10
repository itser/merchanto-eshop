<?php

namespace Modules\Catalog\Filament\Resources\ProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Filament\Resources\ProductResource;
use Modules\Catalog\Services\ProductManagementService;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected ProductManagementService $productManagement;

    public function boot(ProductManagementService $productManagement): void
    {
        $this->productManagement = $productManagement;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return $this->productManagement->create($data);
    }
}
