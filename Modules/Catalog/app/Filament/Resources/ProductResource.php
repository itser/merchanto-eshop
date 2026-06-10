<?php

namespace Modules\Catalog\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\CreateProduct;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\EditProduct;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\ListProducts;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return Builder<Product>
     */
    public static function getEloquentQuery(): Builder
    {
        return app(ProductRepositoryInterface::class)->query();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->default('')
                    ->dehydrateStateUsing(fn (?string $state): string => $state ?? ''),
                TextInput::make('price')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->dehydrateStateUsing(fn ($state) => $state ?? 0),
                TextInput::make('stock')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->dehydrateStateUsing(fn ($state) => $state ?? 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('price')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('stock')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
