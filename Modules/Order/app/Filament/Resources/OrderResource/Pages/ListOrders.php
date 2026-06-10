<?php

namespace Modules\Order\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Order\Filament\Resources\OrderResource;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
