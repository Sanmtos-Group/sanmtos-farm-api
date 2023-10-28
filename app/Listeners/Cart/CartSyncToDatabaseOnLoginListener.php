<?php

namespace App\Listeners\Cart;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Facades\CartFacade;

class CartSyncToDatabaseOnLoginListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        CartFacade::SyncToDatabaseOnLogin();
    }
}
