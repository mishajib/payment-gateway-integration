<?php

namespace App\Http\Controllers\Gateways\Stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey('test_SecretKey');
    }

    public function createPayment(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('stripe.secret'));
        try {
            \Stripe\Charge::create(array(
                                       "amount"      => 300 * 100,
                                       "currency"    => "usd",
                                       "source"      => $request->input('stripeToken'), // obtained with Stripe.js
                                       "description" => "Test payment."
                                   ));
            Session::flash('success-message', 'Payment done successfully !');
            return Redirect::back();
        } catch (\Exception $e) {
            Session::flash('fail-message', "Error! Please Try again.");
            return Redirect::back();
        }
    }
}
