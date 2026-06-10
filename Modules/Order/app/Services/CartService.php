<?php

namespace Modules\Order\Services;

use Illuminate\Contracts\Session\Session;
use Modules\Order\DataTransferObjects\CartLine;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(
        private readonly Session $session,
    ) {}

    public function add(int $productId, int $quantity = 1): void
    {
        $lines = $this->rawLines();

        if (array_key_exists($productId, $lines)) {
            $lines[$productId] += $quantity;
        } else {
            $lines[$productId] = $quantity;
        }

        $this->persist($lines);
    }

    public function remove(int $productId): void
    {
        $lines = $this->rawLines();

        unset($lines[$productId]);

        $this->persist($lines);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    /**
     * @return list<CartLine>
     */
    public function lines(): array
    {
        $result = [];

        foreach ($this->rawLines() as $productId => $quantity) {
            $result[] = new CartLine(
                productId: (int) $productId,
                quantity: (int) $quantity,
            );
        }

        return $result;
    }

    public function totalQuantity(): int
    {
        return array_sum($this->rawLines());
    }

    public function isEmpty(): bool
    {
        return $this->rawLines() === [];
    }

    /**
     * @return array<int, int>
     */
    private function rawLines(): array
    {
        /** @var array<int, int>|null $lines */
        $lines = $this->session->get(self::SESSION_KEY);

        return is_array($lines) ? $lines : [];
    }

    /**
     * @param  array<int, int>  $lines
     */
    private function persist(array $lines): void
    {
        if ($lines === []) {
            $this->clear();

            return;
        }

        $this->session->put(self::SESSION_KEY, $lines);
    }
}
