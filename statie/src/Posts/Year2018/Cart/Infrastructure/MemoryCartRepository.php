<?php

declare(strict_types=1);

namespace Pehapkari\Statie\Posts\Year2018\Cart\Infrastructure;

use Pehapkari\Statie\Posts\Year2018\Cart\Domain\Cart;
use Pehapkari\Statie\Posts\Year2018\Cart\Domain\CartNotFoundException;
use Pehapkari\Statie\Posts\Year2018\Cart\Domain\CartRepositoryInterface;

final class MemoryCartRepository implements CartRepositoryInterface
{
    /**
     * @var Cart[]
     */
    private $carts = [];

    public function add(Cart $cart): void
    {
        $this->carts[$cart->getId()] = $cart;
    }

    public function get(string $id): Cart
    {
        $this->checkExistence($id);

        return $this->carts[$id];
    }

    public function remove(string $id): void
    {
        $this->checkExistence($id);
        unset($this->carts[$id]);
    }

    private function checkExistence(string $id): void
    {
        if (! isset($this->carts[$id])) {
            throw new CartNotFoundException();
        }
    }
}
