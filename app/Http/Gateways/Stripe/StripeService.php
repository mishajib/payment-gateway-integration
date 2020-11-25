<?php

namespace App\Http\Gateways\Stripe;

use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    public function createPayment(Request $request)
    {
        return Charge::create(array(
            "amount"      => 300 * 100,
            "currency"    => "usd",
            "source"      => $request->input('stripeToken'), // obtained with Stripe.js
            "description" => "Test payment.",
        ));
    }
}
