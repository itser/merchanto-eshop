<?php

namespace Modules\Order\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Modules\Order\Filament\Resources\OrderResource;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderManagementService;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected OrderManagementService $orderManagement;

    public function boot(OrderManagementService $orderManagement): void
    {
        $this->orderManagement = $orderManagement;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Order) {
            throw new InvalidArgumentException('Expected order record.');
        }

        return $this->orderManagement->update($record, $data);
    }
}
