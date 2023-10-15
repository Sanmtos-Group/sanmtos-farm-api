<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Cart extends Model
{
    use HasFactory;
    public mixed $quantity;

    public $grandTotalWeight;

    public static function add($cartData)
    {
        $cart = session('cart', []);

        $cartId = rand(000000,999999); //generates the random CartId for every item

        $cart[$cartId] = $cartData;

        return session()->put('cart', $cart);
    }

    public function GrandTotalWeight()
    {
        $cartItems = self::getContent();

        if($cartItems !== NULL &&  count($cartItems) > 0){

            foreach ($cartItems as $key => $value) {

                //the $value['price'] & $value['quantity'] should be passed
                //in cart array or it wont be able to calculate the total
                $this->grandTotalWeight += $value['price'] * $value['quantity'];
            }
        }

        return $this->grandTotalWeight;
    }

    public static function destroyCartItem($cartId)
    {
        $cartItems = \App\Services\Cart::getContent();

        //find the cart item and remove it by using unset method
        unset($cartItems[$cartId]);

        Session::put('cart', $cartItems); //update the array

        return;
    }


//    public static function destroyCartItem($cartId)
//    {
//
//        if(Session::has('cart'){
//
//      return Session::forget('cart'); //update the array
//    }
//
//        return;
//    }


    public static function EmptyCheck()
    {
        $cartItems = self::getContent();

        if($cartItems !== NULL && count($cartItems) > 0 ){

            return true;
        }

        return false;

    }
}
