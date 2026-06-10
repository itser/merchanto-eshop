<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddToCart extends Component
{
    public function __construct(
        public int $productId,
        public bool $canAdd = true,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.add-to-cart');
    }
}
