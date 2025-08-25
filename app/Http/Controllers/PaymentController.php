<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoMoService;

class PaymentController extends Controller
{
    /**
     * Khởi tạo thanh toán MoMo
     */
    public function initiatePayment()
    {
        try {
            $momoService = new MoMoService();

            // Ví dụ dữ liệu test
            $orderId = 'ORDER_' . time(); // mã đơn hàng duy nhất
            $amount = 100000; // số tiền
            $redirectUrl = route('payment.return'); // route returnPayment

            $paymentResponse = $momoService->createPayment(
                $amount,
                $orderId,
                $redirectUrl
            );

            // Kiểm tra kết quả từ MoMo
            if (isset($paymentResponse['payUrl']) && $paymentResponse['resultCode'] == 0) {
                return redirect($paymentResponse['payUrl']);
            }

            return back()->with('error', 'Không thể tạo giao dịch. Vui lòng thử lại!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý khi MoMo redirect về
     */
    public function returnPayment(Request $request)
    {
        $data = $request->all();

        // Kiểm tra MoMo trả về
        if (isset($data['resultCode']) && $data['resultCode'] == 0) {
            return view('payment.success', [
                'orderId' => $data['orderId'] ?? '',
                'amount'  => $data['amount'] ?? '',
                'message' => 'Thanh toán thành công!'
            ]);
        }

        return view('payment.fail', [
            'message' => $data['message'] ?? 'Thanh toán thất bại!'
        ]);
    }
}
