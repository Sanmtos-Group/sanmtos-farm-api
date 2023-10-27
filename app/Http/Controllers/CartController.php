<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Product;
use App\Facades\CartFacade;
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
        $cart_items = CartFacade::content();

        return response()->json([
            'status' => 'OK',
            'data' => $cart_items,
            'message' => count($cart_items)? 'Cart items retrieved successfully' : 'You do not have an item in your cart yet!',
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
        $validated = $request->validated();
        
        $product = Product::find($validated['product_id']);

        $cart_items = CartFacade::add($product->id, $product->name, $product->price, $validated['quantity']);
        $cart_items = CartFacade::content();

        return response()->json([
            'status' => 'OK',
            'data' => $cart_items,
            'message' => count($cart_items)? 'Cart items retrieved successfully' : 'You do not have an item in your cart yet!',
        ], 200);


        // $user_id = auth()->user()->id;

        if ($user_id && $product){
            $total = $product->price;
            $cart = \App\Models\Cart::create([
                "user_id" => $user_id,
                "product_id" => $product->id,
                "product_name" => $product->name,
                "product_image" => $product->image,
                "quantity" => 1,
                "price" => $product->price,
                "total_price" => $total
            ]);

            return response()->json([
                'status' => 'OK',
                'data' => $cart,
                'message' => count($cart)? 'Cart items retrived sucessfully' : 'You do not have an item in your cart yet!',
            ], 201);
        }else {
            if ($product) {
                Cart::add($product->id, $product->name, $product->price, 1, [$product->image]);
            }

            $cartItems = Cart::getContent();

            return response()->json([
                'status' => 'OK',
                'data' => $cartItems,
                'message' => count($cartItems) ? 'Cart items retrived sucessfully' : 'You do not have an item in your cart yet!',
            ], 201);
        }
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
