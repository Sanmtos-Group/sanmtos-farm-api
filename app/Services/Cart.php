<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Session\SessionManager;

class Cart {
    const MINIMUM_QUANTITY = 1;
    const DEFAULT_INSTANCE = 'shopping-cart';

    protected static $session;
    protected $instance;

    /**
     * Constructs a new cart object.
     *
     * @param Illuminate\Session\SessionManager $session
     */
    public function __construct(SessionManager $session)
    {
        self::$session = $session;
    }

    /**
     * Adds a new item to the cart.
     *
     * @param string $id
     * @param string $name
     * @param string $price
     * @param int $quantity
     * @param array $options
     * @return void
     */
    public static function add($id, $name, $price, $quantity, $options = []): void
    {
        $cartItem = self::createCartItem($name, $price, $quantity, $options);

        $content = self::getContent();

        if ($content->has($id)) {
            $cartItem->put('quantity', $content->get($id)->get('quantity') + $quantity);
        }

        $content->put($id, $cartItem);

        self::$session->put(self::DEFAULT_INSTANCE, $content);
    }

    /**
     * Updates the quantity of a cart item.
     *
     * @param string $id
     * @param string $action
     * @return void
     */
    public static function update(string $id, string $action): void
    {
        $content = self::getContent();

        if ($content->has($id)) {
            $cartItem = $content->get($id);

            switch ($action) {
                case 'plus':
                    $cartItem->put('quantity', $content->get($id)->get('quantity') + 1);
                    break;
                case 'minus':
                    $updatedQuantity = $content->get($id)->get('quantity') - 1;

                    if ($updatedQuantity < self::MINIMUM_QUANTITY) {
                        $updatedQuantity = self::MINIMUM_QUANTITY;
                    }

                    $cartItem->put('quantity', $updatedQuantity);
                    break;
            }

            $content->put($id, $cartItem);

            self::$session->put(self::DEFAULT_INSTANCE, $content);
        }
    }

    /**
     * Removes an item from the cart.
     *
     * @param string $id
     * @return void
     */
    public function remove(string $id): void
    {
        $content = self::getContent();

        if ($content->has($id)) {
            $this->session->put(self::DEFAULT_INSTANCE, $content->except($id));
        }
    }

    /**
     * Clears the cart.
     *
     * @return void
     */
    public function clear(): void
    {
        self::$session->forget(self::DEFAULT_INSTANCE);
    }

    /**
     * Returns the content of the cart.
     *
     * @return Illuminate\Support\Collection
     */
    public function content(): Collection
    {
        return is_null($this->session->get(self::DEFAULT_INSTANCE)) ? collect([]) : $this->session->get(self::DEFAULT_INSTANCE);
    }

    /**
     * Returns total price of the items in the cart.
     *
     * @return string
     */
    public function total(): string
    {
        $content = $this->getContent();

        $total = $content->reduce(function ($total, $item) {
            return $total += $item->get('price') * $item->get('quantity');
        });

        return number_format($total, 2);
    }


    /**
     * Returns the content of the cart.
     *
     * @return Illuminate\Support\Collection
     */
    public static function getContent(): Collection
    {
        if(is_null(self::$session))
        self::$session = session();

        return self::$session->has(self::DEFAULT_INSTANCE) ? self::$session->get(self::DEFAULT_INSTANCE) : collect([]);
    }

    /**
     * Creates a new cart item from given inputs.
     *
     * @param string $product_id
     * @param string $name
     * @param string $price
     * @param string $quantity
     * @param array $options
     * @return Illuminate\Support\Collection
     */
    protected static function createCartItem(string $product_id, string $name, string $price, int $quantity, array $options): Collection
    {
        $price = floatval($price);
        $quantity = intval($quantity);

        if ($quantity < self::MINIMUM_QUANTITY) {
            $quantity = self::MINIMUM_QUANTITY;
        }

        return collect([
            'product_id' => $product_id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'options' => $options,
        ]);
    }
}
