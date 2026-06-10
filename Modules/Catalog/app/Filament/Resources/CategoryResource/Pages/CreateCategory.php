<?php

namespace Modules\Catalog\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Filament\Resources\CategoryResource;
use Modules\Catalog\Services\CategoryManagementService;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected CategoryManagementService $categoryManagement;

    public function boot(CategoryManagementService $categoryManagement): void
    {
        $this->categoryManagement = $categoryManagement;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return $this->categoryManagement->create($data);
    }
}
