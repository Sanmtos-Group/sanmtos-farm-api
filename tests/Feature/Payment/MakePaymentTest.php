<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MakePaymentTest extends TestCase{
    public function test_user_can_make_payment(){
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $route = route('payments');
        $response = $this->postJson($route, [
            "amount" => 100,
            "email" => $user->email,
            "product_id" => "9aa8663b-cfc9-4147-8319-235f6925c386",
            "user_id" => $user->id,
            "payment_type" => "order"
        ]);

        $response->assertCreated();
    }
}
