<?php

namespace App\Services;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PaypalService
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),
                env('PAYPAL_SECRET')
            )
        );
        $this->apiContext->setConfig([
            'mode' => env('PAYPAL_MODE', 'sandbox'),
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => storage_path('logs/paypal.log'),
            'log.LogLevel' => 'ERROR'
        ]);
    }

    public function createPayment($order, $totalInUsd)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName('Order #' . $order->order_number)
             ->setCurrency('USD')
             ->setQuantity(1)
             ->setPrice($totalInUsd);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal($totalInUsd);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setItemList($itemList)
                    ->setDescription('Payment for Order #' . $order->order_number);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('customer.paypal.success', $order->id))
                     ->setCancelUrl(route('customer.checkout'));

        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

        $payment->create($this->apiContext);
        return $payment->getApprovalLink();
    }
}
