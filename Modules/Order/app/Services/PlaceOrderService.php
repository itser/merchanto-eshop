<?php

namespace Modules\Order\Services;

use App\Contracts\Catalog\ProductCatalogInterface;
use App\Exceptions\Catalog\InsufficientStockException;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class PlaceOrderService
{
    public function __construct(
        private readonly ProductCatalogInterface $productCatalog,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * @param  list<array{product_id: int, quantity: int}>  $items
     */
    public function place(string $customerName, string $customerEmail, array $items): Order
    {
        $order = DB::transaction(function () use ($customerName, $customerEmail, $items): Order {
            $orderItems = [];
            $total = '0.00';

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];

                $product = $this->productCatalog->findById($productId);

                if ($product === null || ! $this->productCatalog->hasStock($productId, $quantity)) {
                    throw new InsufficientStockException($productId, $quantity);
                }

                $this->productCatalog->decrementStock($productId, $quantity);

                $lineTotal = bcmul($product->price, (string) $quantity, 2);
                $total = bcadd($total, $lineTotal, 2);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $quantity,
                ];
            }

            return $this->orderRepository->createWithItems(
                [
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'total' => $total,
                    'status' => OrderStatus::Pending,
                ],
                $orderItems,
            );
        });

        event(new OrderPlaced($order));

        return $order;
    }
}
