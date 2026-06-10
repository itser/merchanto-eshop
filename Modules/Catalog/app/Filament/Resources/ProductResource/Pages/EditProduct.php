<?php

namespace Modules\Catalog\Filament\Resources\ProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Modules\Catalog\Filament\Resources\ProductResource;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Services\ProductManagementService;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected ProductManagementService $productManagement;

    public function boot(ProductManagementService $productManagement): void
    {
        $this->productManagement = $productManagement;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->using(function (Product $record): bool {
                    $this->productManagement->delete($record);

                    return true;
                }),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Product) {
            throw new InvalidArgumentException('Expected product record.');
        }

        return $this->productManagement->update($record, $data);
    }
}
