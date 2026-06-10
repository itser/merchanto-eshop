<?php

namespace Modules\Catalog\Providers;

use Filament\Panel;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Catalog\Filament\Resources\CategoryResource;
use Modules\Catalog\Filament\Resources\ProductResource;
use Modules\Catalog\Repositories\Contracts\CategoryRepositoryInterface;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Repositories\Eloquent\EloquentCategoryRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentProductRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CatalogServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Catalog';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'catalog';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);

        Panel::configureUsing(function (Panel $panel): void {
            if ($panel->getId() !== 'admin') {
                return;
            }

            $panel->resources([
                CategoryResource::class,
                ProductResource::class,
            ]);
        });
    }

    /**
     * Define module schedules.
     *
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
