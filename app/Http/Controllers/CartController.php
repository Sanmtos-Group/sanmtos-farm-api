<?php

namespace App\Http\Controllers;

//use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
//use Gloudemans\Shoppingcart\Cart;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Support\Facades\Request;

class CartController extends Controller
{
    public $quantity;

    public function mount(): void
    {
        $this->quantity = 1;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartItems = Cart::getContent();

        return response()->json([
            'status' => 'OK',
            'data' => $cartItems,
            'message' => count($cartItems)? 'Cart items retrived' : 'You do not have an item in your cart yet!',
        ], 200);
        
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        $carts = Cart::get($cart);

        return response()->json([
            'message' => "OK",
            'data' => $carts,
        ], 200);
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
    public function update(UpdateCartRequest $request, Cart $cart)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        Cart::remove($cart);

        return response()->json([
            'message' => "Item deleted successfully",
        ], 200);
    }

    /**
     *Add products to cart
     */
    public function addToCart(Request $request)
    {
        $products = Product::find($request->id);
        if ($products) {
            $carts = Cart::add($products->id, $products->name, $products->price, $this->quantity);

            return response()->json([
                'message' => "Item added successfully",
                'data' => $carts
            ], 201);
        }
    }


    /**
     * Clears the cart content.
     *
     * @return void
     */
    public function clearCart(): void
    {
        Cart::clear();
    }

}
