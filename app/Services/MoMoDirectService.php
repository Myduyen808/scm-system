<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoMoDirectService
{
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->partnerCode = env('MOMO_PARTNER_CODE');
        $this->accessKey = env('MOMO_ACCESS_KEY');
        $this->secretKey = env('MOMO_SECRET_KEY');
        $this->baseUrl = 'https://test-payment.momo.vn:22446'; // Sandbox URL
    }

    protected function generateSignature($data)
    {
        $rawSignature = http_build_query($data, '', '&');
        return hash_hmac('sha256', $rawSignature, $this->secretKey);
    }

    public function createOrder($totalVnd, $orderId, $returnUrl, $notifyUrl)
    {
        $requestId = time();
        $data = [
            'partnerCode' => $this->partnerCode,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'amount' => number_format($totalVnd, 0, '', ''),
            'lang' => 'vi',
            'returnUrl' => $returnUrl,
            'notifyUrl' => $notifyUrl,
            'requestType' => 'captureMoMoWallet',
        ];

        $data['signature'] = $this->generateSignature($data);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/api/gw_payment/init", $data);

        if ($response->failed()) {
            Log::error('MoMo CreateOrder Failed: ' . $response->body());
            throw new \Exception('MoMo CreateOrder Failed: ' . $response->body());
        }

        return $response->json();
    }

    public function verifyPayment($orderId)
    {
        // Logic verify sẽ được cập nhật sau khi nhận notifyUrl từ MoMo
        return true; // Placeholder, cần thay bằng logic thực tế
    }
}
