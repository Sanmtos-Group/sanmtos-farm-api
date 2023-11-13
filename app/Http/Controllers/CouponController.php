<?php

namespace App\Http\Controllers;

use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Product;

class CouponController extends Controller
{
    public function __construct(){
        $this->authorizeResource(Coupon::class, 'coupon');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Coupon::all();
        $coupon_resource = new CouponResource();
        $coupon_resource->with['message'] = "Coupon retrieved successfully";
        return $coupon_resource;
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
    public function store(StoreCouponRequest $request)
    {
        $validations = $request->validated();

        $product = Product::where('id', $validations->product_id);

        $coupon_check = Coupon::where('couponable_id', $product->id, 'code', $validations);

        if(! $coupon_check){
            foreach ($validations as $validation){
                $coupon = new Coupon();
                $coupon->code = $validation->code;
                $coupon->discount = $validation->discount;
                $coupon->valid_until = $validation->valid_until;
                $coupon->couponable_type = "App\Models\Products";
                $coupon->couponable_id = $validation->id;
                $coupon->save();
            }

            return response()->json([
                "message" => "Coupon created successfully",
            ], 201);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $coupons = new CouponResource($coupon);
        $coupons->with['message'] = "Coupon retrieved successfully.";

        return $coupons;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());
        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Coupon updated successfully';

        return $coupon_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        $coupon_resource = new CouponResource(null);
        $coupon_resource->with['message'] = "Coupon deleted successfully";

        return $coupon_resource;
    }

    /**
     * Continue a specific coupon running
    */

    public function continue(Coupon $coupon){
        $coupon->is_cancel = true;
        $coupon->save();

        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = "Coupon continued successfully";

        return $coupon_resource;
    }

    /**
     * Cancel a specific running coupon
     */

    public function cancle(Coupon $coupon){
        $coupon->is_cancel = false;
        $coupon->save();

        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = "Coupon cancelled successfully";
    }
}
