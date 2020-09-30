<?php

namespace App\Http\Controllers;


class PageController extends Controller
{
    public function paypal()
    {
        return view('payment');
    }

    public function stripe()
    {
        return view('stripe');
    }
}
