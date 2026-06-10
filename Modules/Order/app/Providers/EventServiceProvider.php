<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Listeners\ClearCartOnOrderPlaced;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = false;

    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        OrderPlaced::class => [
            ClearCartOnOrderPlaced::class,
        ],
    ];

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
