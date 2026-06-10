<?php

namespace Modules\Order\Listeners;

use Modules\Order\Events\OrderPlaced;
use Modules\Order\Services\CartService;

class ClearCartOnOrderPlaced
{
    public function __construct(
        private readonly CartService $cart,
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $this->cart->clear();
    }
}
