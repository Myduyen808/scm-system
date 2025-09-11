<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Order;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        // Thêm CORS headers để tránh lỗi cross-origin
        if ($request->isMethod('options')) {
                return response('', 200)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
            }

            $config = [
                'web' => [
                    'matchingData' => [
                        'driver' => 'web',
                    ],
                ],
            ];

            DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
            $botman = BotManFactory::create($config);

            // Xử lý view /chat nếu là GET request
            if ($request->isMethod('get') && $request->path() === 'chat') {
                return view('chat');
            }

            // Các lệnh chatbot
            $botman->hears('xin chào|hello|hi|chào|chào bạn', function (BotMan $bot) {
                $bot->reply("Xin chào! Tôi là chatbot hỗ trợ SCM System. Tôi có thể giúp bạn:\n\n🔹 Kiểm tra trạng thái đơn hàng\n🔹 Tìm hiểu sản phẩm\n🔹 Chính sách đổi trả\n🔹 Phân tích đánh giá sản phẩm\n\nHãy thử hỏi tôi!");
            });

        $botman->hears('trạng thái đơn hàng {orderId}', function (BotMan $bot, $orderId) {
            $order = Order::where('order_number', $orderId)->first();
            if ($order) {
                $statusText = [
                    'pending'    => 'Đang chờ xử lý',
                    'processing' => 'Đang xử lý',
                    'shipped'    => 'Đã giao vận',
                    'delivered'  => 'Đã giao hàng',
                    'cancelled'  => 'Đã hủy'
                ];
                $status = $statusText[$order->status] ?? $order->status;

                // Lấy đúng tổng tiền
                $amount = $order->total_amount ?? $order->total;

                $bot->reply("📦 Trạng thái đơn hàng #{$orderId}:\n\n".
                    "✅ Trạng thái: {$status}\n".
                    "💰 Tổng tiền: " . number_format($amount, 0, ',', '.') . " VND\n".
                    "📅 Ngày đặt: " . $order->created_at->format('d/m/Y H:i'));
            } else {
                $bot->reply("❌ Không tìm thấy đơn hàng #{$orderId}.\n\nVui lòng kiểm tra lại mã đơn hàng hoặc liên hệ hotline: 1900-1234");
            }
        });

        $botman->hears('chính sách đổi trả|đổi trả|bảo hành', function (BotMan $bot) {
            $bot->reply("📋 CHÍNH SÁCH ĐỔI TRẢ:\n\n✅ Thời gian: Trong vòng 7 ngày kể từ ngày nhận hàng\n✅ Điều kiện: Sản phẩm còn nguyên seal, chưa sử dụng\n✅ Giấy tờ: Có hóa đơn mua hàng\n✅ Phí ship: Miễn phí nếu lỗi từ shop\n\n📞 Hotline hỗ trợ: 1900-1234");
        });

        $botman->hears('có sản phẩm gì|sản phẩm|danh mục', function (BotMan $bot) {
            $bot->reply("🛍️ DANH MỤC SẢN PHẨM:\n\n👕 Thời trang nam nữ\n👟 Giày dép các loại\n🎒 Balo & túi xách\n⌚ Phụ kiện thời trang\n📱 Ốp lưng điện thoại\n\nBạn muốn xem loại sản phẩm nào?");
        });

        $botman->hears('sản phẩm hot|best seller|bán chạy', function (BotMan $bot) {
            $bot->reply("🔥 SẢN PHẨM HOT NHẤT HIỆN TẠI:\n\n🥇 Áo hoodie unisex - 299.000đ\n🥈 Giày sneaker thể thao - 450.000đ  \n🥉 Balo laptop cao cấp - 199.000đ\n🏅 Túi tote canvas - 89.000đ\n🏅 Ốp iPhone trong suốt - 45.000đ\n\n💫 Tất cả đều đang giảm giá 20%!");
        });

        $botman->hears('giá cả|giá|bao nhiêu tiền', function (BotMan $bot) {
            $bot->reply("💰 BẢNG GIÁ THAM KHẢO:\n\n👕 Áo thun: 89.000 - 199.000đ\n👖 Quần jean: 299.000 - 599.000đ\n👟 Giày: 199.000 - 999.000đ\n🎒 Balo: 149.000 - 399.000đ\n\n🎉 Đang có chương trình giảm giá đến 50%!\n💳 Hỗ trợ trả góp 0% lãi suất");
        });

        $botman->hears('thanh toán|payment|trả tiền', function (BotMan $bot) {
            $bot->reply("💳 PHƯƠNG THỨC THANH TOÁN:\n\n🏪 Thu hộ COD (Miễn phí)\n💰 Chuyển khoản ngân hàng\n📱 Ví điện tử (Momo, ZaloPay)\n💳 Thẻ tín dụng/ghi nợ\n\n✅ Tất cả đều an toàn & bảo mật 100%");
        });

        $botman->hears('giao hàng|ship|vận chuyển', function (BotMan $bot) {
            $bot->reply("🚚 CHÍNH SÁCH GIAO HÀNG:\n\n⚡ Nhanh: 2-4h trong nội thành\n🚛 Tiêu chuẩn: 1-3 ngày toàn quốc\n📦 Miễn phí: Đơn từ 200.000đ\n📞 Gọi trước khi giao: 30 phút\n\n🎯 Tỷ lệ giao thành công: 99.8%");
        });

        // -----------------------
        // Phân tích cảm xúc review
        // -----------------------
        $botman->hears('đánh giá (.+)|review (.+)', function (BotMan $bot, $reviewText) {
            $bot->reply("🔄 Đang phân tích đánh giá của bạn...");

            try {
                $response = Http::timeout(10)->post('http://localhost:5001/analyze', [
                    'review' => $reviewText
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $sentiment = $data['sentiment'] ?? '3 stars';
                    $confidence = $data['confidence'] ?? 0.5;

                    $emoji = '';
                    switch ($sentiment) {
                        case '5 stars':
                            $emoji = '😍';
                            break;
                        case '4 stars':
                            $emoji = '😊';
                            break;
                        case '3 stars':
                            $emoji = '😐';
                            break;
                        case '2 stars':
                            $emoji = '😕';
                            break;
                        case '1 star':
                            $emoji = '😞';
                            break;
                        default:
                            $emoji = '🤔';
                    }

                    $bot->reply("✅ KỂT QUẢ PHÂN TÍCH:\n\n{$emoji} Cảm xúc: {$sentiment}\n📊 Độ tin cậy: " . round($confidence * 100) . "%\n\n💬 Cảm ơn bạn đã chia sẻ đánh giá!");
                } else {
                    $bot->reply("❌ Lỗi khi phân tích đánh giá (Mã: {$response->status()})\n\n🔧 Vui lòng thử lại sau hoặc liên hệ hotline: 1900-1234");
                }
            } catch (\Exception $e) {
                $bot->reply("❌ Không thể kết nối đến hệ thống phân tích\n\n📞 Liên hệ hotline: 1900-1234 để được hỗ trợ");
            }
        });

        $botman->hears('phân tích review {reviewId}', function (BotMan $bot, $reviewId) {
            $bot->reply("🔄 Đang phân tích review #{$reviewId}...");

            try {
                $response = Http::timeout(10)->post('http://localhost:5001/analyze', [
                    'review_id' => $reviewId
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $bot->reply("✅ Đã phân tích review #{$reviewId} thành công!\n\n📊 Kết quả đã được lưu vào hệ thống.");
                } else {
                    $bot->reply("❌ Không thể phân tích review #{$reviewId}\n\n🔍 Vui lòng kiểm tra ID review có đúng không");
                }
            } catch (\Exception $e) {
                $bot->reply("❌ Lỗi kết nối hệ thống phân tích");
            }
        });

        $botman->hears('hướng dẫn|help|giúp đỡ', function (BotMan $bot) {
            $bot->reply("📚 HƯỚNG DẪN SỬ DỤNG CHATBOT:\n\n🔹 'xin chào' - Chào hỏi\n🔹 'trạng thái đơn hàng 123' - Kiểm tra đơn\n🔹 'sản phẩm hot' - Xem hàng bán chạy\n🔹 'chính sách đổi trả' - Thông tin đổi trả\n🔹 'đánh giá [nội dung]' - Phân tích review\n🔹 'giao hàng' - Thông tin vận chuyển\n\n💡 Tôi học AI nên hiểu được nhiều cách hỏi khác nhau!");
        });

        $botman->hears('cảm ơn|thank you|thanks', function (BotMan $bot) {
            $bot->reply("🙏 Rất vui được hỗ trợ bạn!\n\nNếu cần thêm trợ giúp, hãy nhắn tin cho tôi bất cứ lúc nào. Chúc bạn mua sắm vui vẻ! 😊");
        });

        // -----------------------
        // Fallback cuối cùng
        // -----------------------
    $botman->fallback(function (BotMan $bot) {
        $bot->reply("🤔 Xin lỗi, tôi chưa hiểu ý bạn.\n\n💡 BạN CÓ THỂ THỬ:\n🔹 'trạng thái đơn hàng 123'\n🔹 'sản phẩm hot' \n🔹 'chính sách đổi trả'\n🔹 'hướng dẫn'\n\n📞 Hoặc gọi hotline: 1900-1234");
    });

    $botman->listen();

    // Thêm header CORS cho mọi response
    return response('')->header('Access-Control-Allow-Origin', '*');
    }
}
