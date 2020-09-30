<?php


namespace App\Http\Gateways\Paypal;


use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

class ExecutePayment extends Paypal
{
    public function execute()
    {
        /** Get the payment ID before session clear **/
        $payment = $this->getPayment();


        if (empty(request('PayerID')) || empty(request('token'))) {
            Session::put('error', 'Payment failed');

            /** clear the session payment ID **/
            $this->forgetPaymentId();
            return $this->redirectPage();
        }

        // create execution
        /**Execute the payment **/
        $result = $payment->execute($this->createExecution(), $this->_api_context);
        if ($result->getState() == 'approved') {
            Session::put('success', 'Payment success');

            /** clear the session payment ID **/
            $this->forgetPaymentId();
            return $this->redirectPage();
        }
        Session::put('error', 'Payment failed');

        /** clear the session payment ID **/
        $this->forgetPaymentId();
        return $this->redirectPage();
    }

    protected function forgetPaymentId(): void
    {
        Session::forget('paypal_payment_id');
    }

    /**
     * @return RedirectResponse
     */
    protected function redirectPage(): RedirectResponse
    {
        return Redirect::route('payment.page');
    }

    /**
     * @return Payment
     */
    protected function getPayment(): Payment
    {
        $payment_id = Session::get('paypal_payment_id');
        $payment    = Payment::get($payment_id, $this->_api_context);
        return $payment;
    }

    /**
     * @return PaymentExecution
     */
    protected function createExecution(): PaymentExecution
    {
        $execution = new PaymentExecution();
        $execution->setPayerId(request('PayerID'));
        return $execution;
    }
}