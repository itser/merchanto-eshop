<?php

namespace Modules\Order\Filament\Resources;

use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Filament\Resources\OrderResource\Pages\EditOrder;
use Modules\Order\Filament\Resources\OrderResource\Pages\ListOrders;
use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'id';

    /**
     * @return Builder<Order>
     */
    public static function getEloquentQuery(): Builder
    {
        return app(OrderRepositoryInterface::class)->query();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_name')
                    ->disabled(),
                TextInput::make('customer_email')
                    ->disabled(),
                TextInput::make('total')
                    ->disabled(),
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->searchable(),
                TextColumn::make('total')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
