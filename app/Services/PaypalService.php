<?php

namespace App\Services;

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
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
            'log.LogEnabled' => false,
        ]);
    }

    public function createPayment($order, $totalInUsd)
    {
        // Validate input
        $totalInUsd = round(floatval($totalInUsd), 2);

        if ($totalInUsd <= 0) {
            throw new \Exception('Invalid total amount: ' . $totalInUsd);
        }

        // Tạo payer
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Tạo amount - KHÔNG dùng Items hoặc Details
        $amount = new Amount();
        $amount->setCurrency('USD');
        $amount->setTotal($totalInUsd);

        // Tạo transaction - CHỈ với amount, không có ItemList
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription('Order #' . $order->order_number . ' - SCM System');

        // Redirect URLs
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('customer.paypal.success', ['order' => $order->id]));
        $redirectUrls->setCancelUrl(route('customer.checkout'));

        // Tạo payment
        $payment = new Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setTransactions([$transaction]);
        $payment->setRedirectUrls($redirectUrls);

        // Debug log
        \Log::info('PayPal Payment Creation:', [
            'order_id' => $order->id,
            'total_usd' => $totalInUsd,
            'description' => 'Order #' . $order->order_number
        ]);

        try {
            $payment->create($this->apiContext);

            // Tìm approval URL
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() === 'approval_url') {
                    return $link->getHref();
                }
            }

            throw new \Exception('No approval URL found in PayPal response');

        } catch (\Exception $e) {
            \Log::error('PayPal API Error:', [
                'message' => $e->getMessage(),
                'order_id' => $order->id,
                'total_usd' => $totalInUsd
            ]);
            throw $e;
        }
    }

    public function executePayment($paymentId, $payerId, $order)
    {
        try {
            $payment = Payment::get($paymentId, $this->apiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $this->apiContext);

            \Log::info('PayPal Payment Executed:', [
                'payment_id' => $paymentId,
                'payer_id' => $payerId,
                'state' => $result->state
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('PayPal Execution Error:', [
                'message' => $e->getMessage(),
                'payment_id' => $paymentId,
                'payer_id' => $payerId
            ]);
            throw $e;
        }
    }
}   
