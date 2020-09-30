<?php

use App\Http\Controllers\Gateways\Paypal\PaypalController;
use App\Http\Controllers\Gateways\Stripe\StripeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/payment', [PageController::class, 'paypal'])->name('payment.page');
Route::get('/stripe', [PageController::class, 'stripe'])->name('stripe.page');
Route::post('/paypal', [PaypalController::class, 'createPayment'])->name('paypal.payment');
Route::get('status', [PaypalController::class, 'executePayment'])->name('paypal.payment.status');
Route::put('/stripe', [StripeController::class, 'createPayment'])->name('stripe.payment');

