<?php

namespace App\Listeners\Cart;

use Illuminate\Auth\Events\Login;
use App\Facades\CartFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncCartToDatabaseListener
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
