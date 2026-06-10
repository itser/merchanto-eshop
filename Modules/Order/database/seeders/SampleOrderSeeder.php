<?php

namespace Modules\Order\Database\Seeders;

use App\Contracts\Catalog\ProductCatalogInterface;
use Illuminate\Database\Seeder;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderManagementService;
use Modules\Order\Services\PlaceOrderService;
use RuntimeException;

class SampleOrderSeeder extends Seeder
{
    /**
     * @var list<array{
     *     customer_name: string,
     *     customer_email: string,
     *     status?: OrderStatus,
     *     items: list<array{product_name: string, quantity: int}>
     * }>
     */
    private array $orders = [
        [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane.doe@example.com',
            'items' => [
                ['product_name' => 'Wireless Mouse', 'quantity' => 2],
                ['product_name' => 'USB-C Hub', 'quantity' => 1],
            ],
        ],
        [
            'customer_name' => 'Alex Smith',
            'customer_email' => 'alex.smith@example.com',
            'status' => OrderStatus::Confirmed,
            'items' => [
                ['product_name' => 'Laravel Guide', 'quantity' => 1],
            ],
        ],
        [
            'customer_name' => 'Maria Garcia',
            'customer_email' => 'maria.garcia@example.com',
            'status' => OrderStatus::Shipped,
            'items' => [
                ['product_name' => 'Ceramic Plant Pot', 'quantity' => 2],
            ],
        ],
    ];

    public function run(): void
    {
        $placeOrderService = app(PlaceOrderService::class);
        $orderManagement = app(OrderManagementService::class);
        $catalog = app(ProductCatalogInterface::class);

        foreach ($this->orders as $orderData) {
            $items = [];

            foreach ($orderData['items'] as $item) {
                $items[] = [
                    'product_id' => $this->productIdByName($catalog, $item['product_name']),
                    'quantity' => $item['quantity'],
                ];
            }

            $order = $placeOrderService->place(
                customerName: $orderData['customer_name'],
                customerEmail: $orderData['customer_email'],
                items: $items,
            );

            $status = $orderData['status'] ?? OrderStatus::Pending;

            if ($status !== OrderStatus::Pending) {
                $this->advanceToStatus($orderManagement, $order, $status);
            }
        }
    }

    private function advanceToStatus(
        OrderManagementService $orderManagement,
        Order $order,
        OrderStatus $target,
    ): void {
        while ($order->status !== $target) {
            $next = match ($order->status) {
                OrderStatus::Pending => OrderStatus::Confirmed,
                OrderStatus::Confirmed => OrderStatus::Shipped,
                OrderStatus::Shipped => OrderStatus::Delivered,
                OrderStatus::Delivered => throw new RuntimeException(
                    "Cannot advance order [{$order->id}] from delivered to [{$target->value}].",
                ),
            };

            $order = $orderManagement->update($order, ['status' => $next]);
        }
    }

    private function productIdByName(ProductCatalogInterface $catalog, string $name): int
    {
        foreach ($catalog->listAvailable() as $product) {
            if ($product->name === $name) {
                return $product->id;
            }
        }

        throw new RuntimeException("Seed product not found: {$name}");
    }
}
