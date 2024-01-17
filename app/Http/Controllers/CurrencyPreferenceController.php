<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyPreferenceRequest;
use App\Http\Requests\UpdateCurrencyPreferenceRequest;
use App\Models\CurrencyPreference;

class CurrencyPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreCurrencyPreferenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CurrencyPreference $currencyPreference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CurrencyPreference $currencyPreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrencyPreferenceRequest $request, CurrencyPreference $currencyPreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CurrencyPreference $currencyPreference)
    {
        //
    }
}
