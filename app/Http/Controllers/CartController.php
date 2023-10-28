<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Facades\CartFacade;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CartController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     
        $cart_items = auth()->user() ? auth()->user()->cartItems :  CartFacade::content();

        $cart_resource =  new CartResource($cart_items);
        $cart_resource->with['message'] = 'Cart items retrived successfully';
        
        return $cart_resource;
    }

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {

        $cart_resource =  new CartResource($cart);
        $cart_resource->with['message'] = 'Cart item retrived successfully';
        
        return $cart_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, Cart $cart=null)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        $cart_resource = new CartResource(null);
        $cart_resource->with['message'] = 'Cart item deleted successfully';
        
        return $cart_resource;
    }

    /**
     * Get the current user's cart items
     */
    public function items()
    {
     
        $cart_items = auth()->user() ? auth()->user()->cartItems :  CartFacade::content();

        $cart_resource =  new CartResource($cart_items);
        $cart_resource->with['message'] = 'Cart items retrived successfully';
        
        return $cart_resource;
    }

    /**
     * Store item to current user's cart 
     * 
     */
    public function add(StoreCartRequest $request){
        
        $validated = $request->validated();
        
        $product = Product::find($validated['product_id']);

        if(auth()->user())
        {
            $cart_item = auth()->user()->cartItems()->where('product_id', $product->id)->first();

            if(is_null($cart_item)){
                $cart_item = new Cart();
                $cart_item->user_id = auth()->user()->id;
                $cart_item->product_id = $product->id;
                $cart_item->quantity = $validated['quantity'] ?? 1;
            }
            else {
                $cart_item->quantity +=1;
            }

            $cart_item->save() ?? null;

            $cart_items = auth()->user()->cartItems;
        }
        else {
            CartFacade::add($product->id, $product->name, $product->price, $validated['quantity']?? 1);
            $cart_items = CartFacade::content();
        }

        $cart_resource =  new CartResource($cart_items);
        $cart_resource->with['message'] = 'Item added to cart successfully';
        return $cart_resource;
    }

    /**
     * Increment the quantity of a specific user's cart item
     *
     */
    public function increment($item)
    {
        if(auth()->user())
        {
            $cart_item = auth()->user()->cartItems()->where('product_id', $item)->first();
            if(!is_null($cart_item))
            {
                $cart_item->quantity  +=1;
                $cart_item->save();
            }

            $cart_items = auth()->user()->cartItems;
        }
        else {
            CartFacade::update($item, 'plus');
            $cart_items = CartFacade::content();
        }

        $cart_resource =  new CartResource($cart_items);
        $cart_resource->with['message'] = 'Cart item\'s quantity incremented successfully';
        return $cart_resource;
    }

    /**
     * Decrement the quantity of a specific user's cart item
     *
     */
    public function decrement($item)
    {
        if(auth()->user()){
            $cart_item = auth()->user()->cartItems()->where('product_id', $item)->first();

            if(!is_null($cart_item))
            {
                if( $cart_item->quantity > 1)
                {
                    $cart_item->quantity  -=1;
                    $cart_item->save();
                }
                else {
                    $cart_item->delete();
                }
            }

            $cart_items = auth()->user()->cartItems;
        }
        else {
            CartFacade::update($item, 'minus');
            $cart_items = CartFacade::content();
        }

        $cart_resource =  new CartResource($cart_items);
        $cart_resource->with['message'] = 'Cart item\'s quantity decremented successfully';
        return $cart_resource;
    }

    /**
     * Remove the item from current user's cart
     *
     */
    public function remove($item)
    {
        if(auth()->user())
        {
            auth()->user()->cartItems()->where('product_id', $item)->delete();
            $cart_items = auth()->user()->cartItems;
        }
        else {
            CartFacade::remove($item);
            $cart_items = CartFacade::content();
        }

        $cart_resource =  new CartResource($cart_items);
        $cart_resource->with['message'] = 'Item removed cart successfully';
        return $cart_resource;
    }

    /**
     * Clear current user's cart
     *
     */
    public function clear()
    {
        if(auth()->user())
        {
            auth()->user()->cartItems()->delete();
            $cart_items = auth()->user()->cartItems;
        }
        else {
            CartFacade::clear();
            $cart_items = CartFacade::content();
        }

        $cart_resource =  new CartResource($cart_items);
        $cart_resource->with['message'] = 'Cart items cleared successfully';
        return $cart_resource;
    }

}
