<?php

namespace Modules\Order\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Order\Services\CartService;

class CartBadge extends Component
{
    public int $count = 0;

    public function mount(CartService $cart): void
    {
        $this->count = $cart->totalQuantity();
    }

    #[On('cart-updated')]
    public function refreshCount(CartService $cart): void
    {
        $this->count = $cart->totalQuantity();
    }

    public function render()
    {
        return view('order::livewire.cart-badge');
    }
}
