<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Session\SessionManager;

class CheckoutService {
    const DEFAULT_INSTANCE = 'order-summary';

    public static function clearCheckoutSession()
    {
        session()->forget(Self::DEFAULT_INSTANCE);
    }
}