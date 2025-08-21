<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MockMoMoService
{
    public function createOrder($totalVnd, $orderId, $returnUrl, $notifyUrl)
    {
        // Mô phỏng phản hồi thành công từ MoMo
        $mockResponse = [
            'resultCode' => 0, // 0 = Thành công
            'orderId' => $orderId,
            'transId' => 'MOCK_' . time(), // Mã giao dịch giả
            'payUrl' => route('customer.momo.success', $orderId) . '?transId=MOCK_' . time(), // Redirect ngay về success
            'message' => 'Thanh toán MoMo thành công',
        ];

        Log::info('Mock MoMo CreateOrder Response: ' . json_encode($mockResponse));
        return $mockResponse;
    }

    public function verifyPayment($orderId)
    {
        // Mô phỏng xác minh thành công
        return true;
    }
}
