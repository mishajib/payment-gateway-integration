<?php


namespace App\Http\Gateways\Paypal;


use Illuminate\Support\Facades\Config;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal
{
    protected $_api_context;

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
}