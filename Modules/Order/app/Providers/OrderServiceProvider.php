<?php

namespace Modules\Order\Providers;

use Filament\Panel;
use Illuminate\Console\Scheduling\Schedule;
use Livewire\Livewire;
use Modules\Order\Filament\Resources\OrderResource;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;
use Modules\Order\Repositories\Eloquent\EloquentOrderRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class OrderServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Order';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'order';

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

        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);

        Panel::configureUsing(function (Panel $panel): void {
            if ($panel->getId() !== 'admin') {
                return;
            }

            $panel->resources([
                OrderResource::class,
            ]);
        });
    }

    public function boot(): void
    {
        parent::boot();

        Livewire::addNamespace('order', module_path('Order', 'app/Livewire'));
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
