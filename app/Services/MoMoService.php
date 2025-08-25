<?php
namespace App\Services;

use GuzzleHttp\Client;

class MoMoService
{
    protected $client;
    protected $endpoint;

    public function __construct()
    {
        $this->client = new Client();
        // Tạm thời trỏ về mock thay vì API thật
        $this->endpoint = url('/mock/momo/payment');
    }

    public function createPayment($amount, $orderId, $returnUrl)
    {
        $requestData = [
            'orderId' => $orderId,
            'amount' => $amount,
            'returnUrl' => $returnUrl,
        ];

        $response = $this->client->post($this->endpoint, [
            'form_params' => $requestData,
        ]);

        return json_decode($response->getBody(), true);
    }
}
