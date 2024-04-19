<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogisticRequest;
use App\Http\Requests\UpdateLogisticRequest;
use App\Models\Logistic;

class LogisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return true;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogisticRequest $request)
    {
        return true;
    }

    /**
     * Display the specified resource.
     */
    public function show(Logistic $logistic)
    {
        return true;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logistic $logistic)
    {
        return true;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLogisticRequest $request, Logistic $logistic)
    {
        return true;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logistic $logistic)
    {
        return true;
    }
}
