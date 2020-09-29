<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

class PaymentController extends Controller
{
    private $_api_context;

    public function __construct()
    {
        /** PayPal api context **/

        // Get paypal configuration
        $paypal_conf        = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                                                 $paypal_conf['client_id'],
                                                 $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }


    public function payWithPaypal(Request $request)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
//        dd($payer);
        $item_1 = new Item();
        $item_1->setName('Item 1')/** item name **/
               ->setCurrency('USD')
               ->setQuantity(1)
               ->setPrice($request->get('amount'));
        /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal($request->get('amount'));
        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription('Your transaction description');
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('paypal.payment.status'))/** Specify return URL **/
                      ->setCancelUrl(URL::route('paypal.payment.status'));
        $payment = new Payment();
        $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {
            $payment->create($this->_api_context);
        } catch (PayPalConnectionException $ex) {
            if (Config::get('app.debug')) {
                Session::put('error', 'Connection timeout');
                return Redirect::route('payment.page');
            } else {
                Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::route('payment.page');
            }
        }
        foreach ( $payment->getLinks() as $link ) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        Session::put('error', 'Unknown error occurred');
        return Redirect::route('payment.page');
    }

    public function getPaymentStatus()
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');


        if (empty(\request()->input('PayerID')) || empty(\request()->input('token'))) {
            Session::put('error', 'Payment failed');

            /** clear the session payment ID **/
            Session::forget('paypal_payment_id');
            return Redirect::route('payment.page');
        }
        $payment   = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(\request()->input('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') {
            Session::put('success', 'Payment success');

            /** clear the session payment ID **/
            Session::forget('paypal_payment_id');
            return Redirect::route('payment.page');
        }
        Session::put('error', 'Payment failed');

        /** clear the session payment ID **/
        Session::forget('paypal_payment_id');
        return Redirect::route('payment.page');
    }
}
