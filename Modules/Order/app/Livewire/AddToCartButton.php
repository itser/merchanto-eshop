<?php

namespace Modules\Order\Livewire;

use App\Contracts\Catalog\ProductCatalogInterface;
use Livewire\Component;
use Modules\Order\Services\CartService;

class AddToCartButton extends Component
{
    public int $productId;

    public bool $canAdd = true;

    public function mount(int $productId, bool $canAdd = true): void
    {
        $this->productId = $productId;
        $this->canAdd = $canAdd;
    }

    public function addToCart(CartService $cart, ProductCatalogInterface $catalog): void
    {
        if (! $this->canAdd || ! $catalog->hasStock($this->productId, 1)) {
            return;
        }

        $cart->add($this->productId, 1);

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('order::livewire.add-to-cart-button');
    }
}
