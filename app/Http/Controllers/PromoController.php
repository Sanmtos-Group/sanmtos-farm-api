<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Resources\PromoResource;
class PromoController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Promo::class, 'promo');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::all();
        $promo_resource = new PromoResource($promos);
        return $promo_resource;
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
    public function store(StorePromoRequest $request)
    {
        $promo_data = $request->validated();
        $promo = Promo::create($promo_data);

        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo created successfully';

        return $promo_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promo $promo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromoRequest $request, Promo $promo)
    {
        $promo->update($request->validated());
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo updated successfully';

        return $promo_resource;
    }

    /**
     * Cancel the specified resource in storage.
     */
    public function cancel(Promo $promo)
    {
        $promo->is_cancel = false;
        $promo->save();
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo cancelled successfully';

        return $promo_resource;
    }

    /**
     * Continue the specified resource in storage.
     */
    public function continue(Promo $promo)
    {
        $promo->is_cancel = true;
        $promo->save();

        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo continued successfully';

        return $promo_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo)
    {
        //
    }
}
