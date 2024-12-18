<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Session\SessionManager;
use App\Models\Cart;
use App\Models\Product;
class CartService {
    const MINIMUM_QUANTITY = 1;
    const DEFAULT_INSTANCE = 'shopping-cart';

    protected $session;
    protected $instance;

    /**
     * Constructs a new cart object.
     *
     * @param Illuminate\Session\SessionManager $session
     */
    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    /**
     * Adds a new item to the cart.
     *
     * @param string $id
     * @param string $name
     * @param string $price
     * @param string $quantity
     * @param array $options
     * @return void
     */
    public function add($id, $name, $price, $quantity, $options = []): void
    {
        $cartItem = $this->createCartItem($name, $price, $quantity, $options);

        $content = $this->getContent();

        if ($content->has($id)) {
            $cartItem->put('quantity', $content->get($id)->get('quantity') + $quantity);
        }

        $content->put($id, $cartItem);

        $this->session->put(self::DEFAULT_INSTANCE, $content);
    }

    /**
     * Find item in cart by ID
     */
    public function find($id) {
        $content = $this->getContent();

        return $content->has($id) ? $content->get($id) : null;
       
    }

    /**
     * Updates the quantity of a cart item.
     *
     * @param string $id
     * @param string $action
     * @return void
     */
    public function update(string $id, string $action): void
    {
        $content = $this->getContent();

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

            $this->session->put(self::DEFAULT_INSTANCE, $content);
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
        $content = $this->getContent();

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
        $this->session->forget(self::DEFAULT_INSTANCE);
    }

    /**
     * Returns the content of the cart.
     *
     * @return Illuminate\Support\Collection
     */
    public function content(): Collection
    {   
        $this->refreshWithDatabaseInfo();
        return is_null($this->session->get(self::DEFAULT_INSTANCE)) ? collect([]) : $this->session->get(self::DEFAULT_INSTANCE);
    }
    
    /**
     * Returns the content of the cart in array.
     *
     * @return array
     */
    public function contentArray(): array
    {
        $items = [];
        $cart_items = $this->content();

        $cart_items->map(function  ($item, $key) use(&$items) {
            $items [] = $item;
        });

        return $items;
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
     * synchronise cart items to database cart on login
     * 
     * @return void
     */
    public function SyncToDatabaseOnLogin(): void
    {
        $contents = $this->session->has(self::DEFAULT_INSTANCE) ? $this->session->get(self::DEFAULT_INSTANCE) : collect([]);

        
        if(auth()->user()) 
        {
            foreach ($contents as $key => $cartItem) 
            {
                $product = Product::find($key);

                if($product)
                {
                    $cart_item = Cart::firstOrNew([
                        'cartable_id' => $product->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    
                    $cart_item->cartable_type = $product::class;
                    $cart_item->quantity =  $cartItem->get('quantity');
                    $cart_item->options = $cartItem->get('options');
                    $cart_item->save();
                }
            
            }
            
            $this->clear();
        }
        
    }

    /**
     * Refresh cart items  with current product data information
     * 
     * @return void
     */
    private function refreshWithDatabaseInfo(): void
    {
        $contents = $this->session->has(self::DEFAULT_INSTANCE) ? $this->session->get(self::DEFAULT_INSTANCE) : collect([]);

        foreach ($contents as $key => $cartItem) {
            $product = Product::find($key);
            $cartItem->put('name', $product->name?? $contents->get($key)->get('name')?? null);
            $cartItem->put('image_url', $product->images()->first()->url?? $contents->get($key)->get('image_url')?? null);
            $cartItem->put('price', $product->price?? $contents->get($key)->get('price')?? 0);
            $cartItem->put('total_price', $cartItem->get('price') * $contents->get($key)->get('quantity')?? 0);
            $contents->put($key, $cartItem);
        }
    }

    /**
     * Returns the content of the cart.
     *
     * @return Illuminate\Support\Collection
     */
    protected function getContent(): Collection
    {
        $this->refreshWithDatabaseInfo();
        return $this->session->has(self::DEFAULT_INSTANCE) ? $this->session->get(self::DEFAULT_INSTANCE) : collect([]);
    }
  

    /**
     * Creates a new cart item from given inputs.
     *
     * @param string $name
     * @param string $price
     * @param string $quantity
     * @param array $options
     * @return Illuminate\Support\Collection
     */
    protected function createCartItem(string $name, string $price, string $quantity, array $options): Collection
    {
        $price = floatval($price);
        $quantity = intval($quantity);

        if ($quantity < self::MINIMUM_QUANTITY) {
            $quantity = self::MINIMUM_QUANTITY;
        }

        return collect([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'total_price' => $price * $quantity,
            'options' => $options,
        ]);
    }
}