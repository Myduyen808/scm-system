<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaypalDirectService
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = env('PAYPAL_CLIENT_ID');
        $this->secret   = env('PAYPAL_SECRET');
        $this->baseUrl  = env('PAYPAL_MODE', 'sandbox') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    protected function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            throw new \Exception('PayPal Auth Failed: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function createOrder($totalUsd, $returnUrl, $cancelUrl)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($this->baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format((float)$totalUsd, 2, '.', '')
                    ]
                ]],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl
                ]
            ]);

        if ($response->failed()) {
            \Log::error('PayPal CreateOrder Failed: ' . $response->body());
            throw new \Exception('PayPal CreateOrder Failed: ' . $response->body());
        }

        return $response->json();
    }

    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        // PayPal capture API cần một empty JSON object, không phải empty array
        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
            ->withBody('{}', 'application/json') // Gửi empty JSON object
            ->post($this->baseUrl . "/v2/checkout/orders/{$orderId}/capture");

        \Log::info('PayPal Capture Response Status: ' . $response->status());
        \Log::info('PayPal Capture Response Body: ' . $response->body());

        if ($response->failed()) {
            $error = $response->json();
            \Log::error('PayPal Capture Failed: ' . json_encode($error));
            throw new \Exception('PayPal Capture Failed: ' . json_encode($error));
        }

        return $response->json();
    }
}
