<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Review;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SentimentController extends Controller
{
    private $pythonServiceUrl = 'http://localhost:5001';

    public function analyze(Request $request)
    {
        try {
            $reviewText = $request->input('review');
            $customerId = $request->input('user_id', 1); // Default user_id
            $productId = $request->input('product_id', 1); // Default product_id
            $rating = $request->input('rating', null);

            if (empty($reviewText)) {
                return response()->json(['error' => 'Review không được để trống'], 400);
            }

            // Gọi Python service
            $response = Http::timeout(30)->post($this->pythonServiceUrl . '/analyze', [
                'review' => $reviewText,
            ]);

            if (!$response->successful()) {
                Log::error('Python service error: ' . $response->body());
                return response()->json(['error' => 'Lỗi phân tích cảm xúc'], 500);
            }

            $sentimentData = $response->json();

            // Lưu vào database
            $review = Review::create([
                'user_id' => $customerId,
                'product_id' => $productId,
                'rating' => $rating ?? $this->getRatingFromSentiment($sentimentData['sentiment']),
                'comment' => $reviewText,
                'sentiment' => $sentimentData['sentiment'],
                'confidence' => $sentimentData['confidence'],
                'reviewed_at' => now(),
            ]);

            // Lấy tên khách hàng từ bảng users (giả định có quan hệ)
            $customerName = $request->input('customer_name', $review->user->name ?? 'Khách hàng');

            // Xử lý theo cảm xúc
            $responseData = $this->handleSentimentResponse($sentimentData, $review, $customerName);

            return response()->json([
                'success' => true,
                'sentiment' => $sentimentData['sentiment'],
                'confidence' => $sentimentData['confidence'],
                'message' => $responseData['message'],
                'action_taken' => $responseData['action'],
                'review_id' => $review->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Sentiment analysis error: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi hệ thống'], 500);
        }
    }

    public function analyzeExisting(Request $request)
    {
        try {
            $reviewId = $request->input('review_id');

            if (!$reviewId) {
                return response()->json(['error' => 'Review ID required'], 400);
            }

            $review = Review::find($reviewId);

            if (!$review) {
                return response()->json(['error' => 'Review không tồn tại'], 404);
            }

            if ($review->sentiment) {
                return response()->json([
                    'message' => 'Review đã được phân tích',
                    'sentiment' => $review->sentiment,
                    'confidence' => $review->confidence,
                ]);
            }

            $response = Http::timeout(30)->post($this->pythonServiceUrl . '/analyze', [
                'review' => $review->comment,
            ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Lỗi phân tích cảm xúc'], 500);
            }

            $sentimentData = $response->json();

            $review->update([
                'sentiment' => $sentimentData['sentiment'],
                'confidence' => $sentimentData['confidence'],
            ]);

            $customerName = $review->user ? $review->user->name : 'Khách hàng';
            $responseData = $this->handleSentimentResponse($sentimentData, $review, $customerName);

            return response()->json([
                'success' => true,
                'sentiment' => $sentimentData['sentiment'],
                'confidence' => $sentimentData['confidence'],
                'message' => $responseData['message'],
                'review_id' => $review->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Existing review analysis error: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi hệ thống'], 500);
        }
    }

    public function batchAnalyze()
    {
        try {
            $reviews = Review::whereNull('sentiment')->limit(10)->get();
            $results = [];

            foreach ($reviews as $review) {
                try {
                    $response = Http::timeout(30)->post($this->pythonServiceUrl . '/analyze', [
                        'review' => $review->comment,
                    ]);

                    if ($response->successful()) {
                        $sentimentData = $response->json();
                        $review->update([
                            'sentiment' => $sentimentData['sentiment'],
                            'confidence' => $sentimentData['confidence'],
                        ]);
                        $results[] = [
                            'review_id' => $review->id,
                            'sentiment' => $sentimentData['sentiment'],
                            'confidence' => $sentimentData['confidence'],
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Error analyzing review {$review->id}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'processed' => count($results),
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            Log::error('Batch analysis error: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi hệ thống'], 500);
        }
    }

    private function getRatingFromSentiment($sentiment)
    {
        switch ($sentiment) {
            case 'positive':
                return rand(4, 5);
            case 'negative':
                return rand(1, 2);
            default:
                return 3;
        }
    }

    private function handleSentimentResponse($sentimentData, $review, $customerName)
    {
        $sentiment = $sentimentData['sentiment'];
        $confidence = $sentimentData['confidence'];

        switch ($sentiment) {
            case '5 stars':
            case '4 stars':
                return $this->handlePositiveFeedback($customerName, $confidence, $review);
            case '1 star':
            case '2 stars':
                return $this->handleNegativeFeedback($customerName, $confidence, $review);
            default:
                return $this->handleNeutralFeedback($customerName, $confidence, $review);
        }
    }

    private function handlePositiveFeedback($customerName, $confidence, $review)
    {
        Log::info("Positive feedback from: {$customerName}, confidence: {$confidence}");

        $message = "🎉 Cảm ơn {$customerName} đã có trải nghiệm tích cực!\n\n" .
                "✨ **Đánh giá:** {$review->rating} sao\n" .
                "📝 **Nội dung:** \"{$review->comment}\"\n" .
                "🎯 **Độ hài lòng:** " . round($confidence * 100) . "%\n" .
                "🎁 **Ưu đãi:** Giảm 10% lần mua tiếp theo - Mã: HAPPY10\n" .
                "📲 Hãy chia sẻ trên mạng xã hội để nhận thêm ưu đãi!";

        // Gửi email
        \Mail::raw($message, function ($message) use ($customerName) {
            $message->to('customer@example.com')->subject('Cảm ơn bạn đã đánh giá!');
        });

        return ['message' => $message, 'action' => 'positive_reward'];
    }

    private function handleNegativeFeedback($customerName, $confidence, $review)
    {
        $couponCode = 'SORRY' . strtoupper(Str::random(6));

        Log::warning("Negative feedback from: {$customerName}, confidence: {$confidence}, review_id: {$review->id}");

        $message = "😔 Chúng tôi xin lỗi {$customerName} vì trải nghiệm không tốt!\n\n" .
                "📝 **Đánh giá:** {$review->rating} sao\n" .
                "📋 **Vấn đề:** \"{$review->comment}\"\n" .
                "🎁 **Mã bồi thường:** {$couponCode} (Giảm 25%)\n" .
                "📞 **Hotline:** 1900-1234 - Team sẽ liên hệ trong 2h";

        // Gửi job thông báo đội ngũ
        NotifySupportTeam::dispatch($review);

        return [
            'message' => $message,
            'action' => 'negative_compensation',
            'coupon_code' => $couponCode,
        ];
    }

    private function handleNeutralFeedback($customerName, $confidence, $review)
    {
        Log::info("Neutral feedback from: {$customerName}, confidence: {$confidence}");

        $message = "👍 Cảm ơn {$customerName} đã đánh giá!\n\n" .
                   "📝 **Đánh giá:** {$review->rating} sao\n" .
                   "💭 **Nội dung:** \"{$review->comment}\"\n" .
                   "🔍 **Chúng tôi sẽ cải thiện** để bạn hài lòng hơn!";

        return ['message' => $message, 'action' => 'neutral_followup'];
    }

    public function getStats()
    {
        $stats = Review::selectRaw('
            sentiment,
            COUNT(*) as count,
            AVG(confidence) as avg_confidence,
            AVG(rating) as avg_rating
        ')
        ->whereNotNull('sentiment')
        ->groupBy('sentiment')
        ->get();

        $total = Review::count();
        $analyzed = Review::whereNotNull('sentiment')->count();

        return response()->json([
            'stats' => $stats,
            'summary' => [
                'total_reviews' => $total,
                'analyzed_reviews' => $analyzed,
                'pending_analysis' => $total - $analyzed,
                'positive' => Review::where('sentiment', 'LIKE', '%stars')->where('sentiment', '>=', '4 stars')->count(),
                'negative' => Review::where('sentiment', 'LIKE', '%stars')->where('sentiment', '<=', '2 stars')->count(),
                'neutral' => Review::where('sentiment', 'NOT LIKE', '%stars')->orWhere('sentiment', '3 stars')->count(),
            ],
        ]);
    }

    public function healthCheck()
    {
        try {
            $response = Http::timeout(5)->get($this->pythonServiceUrl . '/health');

            if ($response->successful()) {
                return response()->json([
                    'status' => 'healthy',
                    'python_service' => $response->json(),
                    'database' => 'connected',
                    'total_reviews' => Review::count(),
                    'analyzed_reviews' => Review::whereNotNull('sentiment')->count(),
                ]);
            }

            return response()->json(['status' => 'python_service_down'], 503);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 503);
        }
    }
}
