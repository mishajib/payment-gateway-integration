<?php


namespace App\Http\Gateways\Paypal;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConnectionException;

class CreatePayment extends Paypal
{

    public function create()
    {
        $item_1 = new Item();
        $item_1->setName('Item 1')/** item name **/
               ->setCurrency('USD')
               ->setQuantity(1)
               ->setPrice($this->getRequest());
        /** unit price **/
        $item_list = $this->itemList($item_1);

        $payment = $this->payment($item_list);

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

        $redirect_url = $this->paymentGetLinks($payment);
        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        Session::put('error', 'Unknown error occurred');
        return Redirect::route('payment.page');
    }

    /**
     * @return Payer
     */
    protected function payer(): Payer
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        return $payer;
    }

    /**
     * @return Amount
     */
    protected function amount(): Amount
    {
        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal($this->getRequest());
        return $amount;
    }

    /**
     * @param ItemList $item_list
     * @return Transaction
     */
    protected function transaction(ItemList $item_list): Transaction
    {
        $transaction = new Transaction();
        $transaction->setAmount($this->amount())
                    ->setItemList($item_list)
                    ->setDescription('Your transaction description');
        return $transaction;
    }

    /**
     * @return RedirectUrls
     */
    protected function redirectUrls(): RedirectUrls
    {
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('paypal.payment.status'))/** Specify return URL **/
                      ->setCancelUrl(URL::route('paypal.payment.status'));
        return $redirect_urls;
    }

    /**
     * @param ItemList $itemList
     * @return Payment
     */
    protected function payment(ItemList $itemList): Payment
    {
        $payment = new Payment();
        $payment->setIntent('Sale')
                ->setPayer($this->payer())
                ->setRedirectUrls($this->redirectUrls())
                ->setTransactions([$this->transaction($itemList)]);
        return $payment;
    }

    /**
     * @param Payment $payment
     * @return string
     */
    protected function paymentGetLinks(Payment $payment): string
    {
        foreach ( $payment->getLinks() as $link ) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        return $redirect_url;
    }

    /**
     * @param Item $item
     * @return ItemList
     */
    protected function itemList(Item $item): ItemList
    {
        $item_list = new ItemList();
        $item_list->setItems([$item]);
        return $item_list;
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\Request|string
     */
    protected function getRequest()
    {
        return \request('amount');
    }
}