<?php

namespace Modules\Catalog\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Filament\Resources\CategoryResource;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Services\CategoryManagementService;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected CategoryManagementService $categoryManagement;

    public function boot(CategoryManagementService $categoryManagement): void
    {
        $this->categoryManagement = $categoryManagement;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->using(function (Category $record): bool {
                    $this->categoryManagement->delete($record);

                    return true;
                }),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $this->categoryManagement->update($record, $data);
    }
}
