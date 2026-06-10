<?php

namespace Modules\Order\Services;

use App\Contracts\Catalog\ProductCatalogInterface;
use App\Exceptions\Catalog\InsufficientStockException;
use Modules\Order\DataTransferObjects\CheckoutLine;
use Modules\Order\Models\Order;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly ProductCatalogInterface $productCatalog,
        private readonly PlaceOrderService $placeOrderService,
    ) {}

    public function isEmpty(): bool
    {
        return $this->cart->isEmpty();
    }

    /**
     * @return list<CheckoutLine>
     */
    public function lines(): array
    {
        $lines = [];

        foreach ($this->cart->lines() as $cartLine) {
            $product = $this->productCatalog->findById($cartLine->productId);

            if ($product === null) {
                continue;
            }

            $lines[] = new CheckoutLine(
                productId: $product->id,
                productName: $product->name,
                productPrice: $product->price,
                quantity: $cartLine->quantity,
                lineTotal: bcmul($product->price, (string) $cartLine->quantity, 2),
            );
        }

        return $lines;
    }

    public function total(): string
    {
        $total = '0.00';

        foreach ($this->lines() as $line) {
            $total = bcadd($total, $line->lineTotal, 2);
        }

        return $total;
    }

    /**
     * @throws InsufficientStockException
     */
    public function place(string $customerName, string $customerEmail): Order
    {
        $items = array_map(
            fn ($line) => [
                'product_id' => $line->productId,
                'quantity' => $line->quantity,
            ],
            $this->cart->lines(),
        );

        $order = $this->placeOrderService->place($customerName, $customerEmail, $items);

        return $order;
    }
}
