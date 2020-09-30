<?php

namespace App\Http\Controllers\Gateways\Paypal;

use App\Http\Controllers\Controller;
use App\Http\Gateways\Paypal\CreatePayment;
use App\Http\Gateways\Paypal\ExecutePayment;

class PaypalController extends Controller
{
    public function createPayment()
    {
        $payment = new CreatePayment();
        return $payment->create();
    }

    public function executePayment()
    {
        $payment = new ExecutePayment();
        return $payment->execute();
    }
}
