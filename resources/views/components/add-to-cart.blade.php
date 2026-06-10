@livewire(\Modules\Order\Livewire\AddToCartButton::class, [
    'productId' => $productId,
    'canAdd' => $canAdd,
], key('add-to-cart-'.$productId))
