<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
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

Route::get('/payment', [PageController::class, 'index'])->name('payment.page');
Route::post('paypal', [PaymentController::class, 'payWithPaypal'])->name('paypal.payment');

Route::get('status', [PaymentController::class, 'getPaymentStatus'])->name('paypal.payment.status');
