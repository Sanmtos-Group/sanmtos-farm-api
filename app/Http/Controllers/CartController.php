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
        $validated = $request->validated();
        
        $product = Product::find($validated['product_id']);

        if(auth()->user()){
            $cart = new Cart();
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = $product->price;
            $cart->quantity = $validated['quantity'];
            $cart->save();

            $cart_items = Cart::where('user_id', auth()->user()->id)->get();

        }else {
            CartFacade::add($product->id, $product->name, $product->price, $validated['quantity']);
            $cart_items = CartFacade::content();
        }

        return response()->json([
            'status' => 'OK',
            'data' => $cart_items,
            'message' => count($cart_items)? 'Cart items retrieved successfully' : 'You do not have an item in your cart yet!',
        ], 200);


        // $user_id = auth()->user()->id;

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
        Cart::update();
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
//    public function addToCart(Request $request)
//    {
//        $products = Product::find($request->id); return $products->id;
//        if ($products) {
//            $carts = Cart::add($products->id, $products->name, $products->price, $this->quantity);
//
//            return response()->json([
//                'message' => "Item added successfully",
//                'data' => $carts
//            ], 201);
//        }
//    }


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
