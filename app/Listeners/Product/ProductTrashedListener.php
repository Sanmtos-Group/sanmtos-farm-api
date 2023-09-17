<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductTrashed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductTrashedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductTrashed $event): void
    {
        //
    }
}
