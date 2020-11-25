<?php

use App\Http\Controllers\Gateways\Paypal\PaypalController;
use App\Http\Controllers\Gateways\Stripe\StripeController;
use App\Http\Controllers\ImageCompressController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SslCommerzPaymentController;
use Illuminate\Support\Facades\Auth;
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

Route::get('image/compress', [ImageCompressController::class, 'index'])->name('image.compress');
Route::post('image/compress', [ImageCompressController::class, 'store'])->name('image.compress');

// SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout'])->name('ajax.page');
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout'])->name('hosted.page');

Route::post('/credit-pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/credit-success', [SslCommerzPaymentController::class, 'success']);
Route::post('/credit-fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/credit-cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/credit-ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END

Auth::routes(['verify' => true]);

Route::group(['prefix' => 'admin'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
