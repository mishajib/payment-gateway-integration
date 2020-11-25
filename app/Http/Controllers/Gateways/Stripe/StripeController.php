<?php

namespace App\Http\Controllers\Gateways\Stripe;

use App\Http\Controllers\Controller;
use App\Http\Gateways\Stripe\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StripeController extends Controller
{
    private $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createPayment(Request $request)
    {
        try {
            $this->stripeService->createPayment($request);
            Session::flash('success-message', 'Payment done successfully !');
            return back();
        } catch (\Exception $e) {
            Session::flash('fail-message', "Error! Please Try again.");
            return back();
        }
    }
}
