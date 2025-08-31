<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function createNotification($userId, $type, $title, $message, $data = null, $relatedId = null, $relatedType = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'is_read' => false
        ]);
    }

    public function notifyEmployeeRequestResponse($requestModel, $employeeId)
    {
        $status = $requestModel->status;
        $statusText = [
            'accepted' => 'đã chấp nhận',
            'rejected' => 'đã từ chối'
        ];

        $title = 'Phản hồi yêu cầu nhập hàng';
        $message = "Nhà cung cấp {$statusText[$status]} yêu cầu nhập hàng #{$requestModel->id} cho sản phẩm {$requestModel->product->name}";

        if ($requestModel->note_from_supplier) {
            $message .= ". Ghi chú: {$requestModel->note_from_supplier}";
        }

        return $this->createNotification(
            $employeeId,
            'stock_request_response',
            $title,
            $message,
            [
                'request_id' => $requestModel->id,
                'status' => $status,
                'supplier_name' => $requestModel->supplier->name,
                'product_name' => $requestModel->product->name
            ],
            $requestModel->id,
            'RequestModel'
        );
    }

    public function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->update(['is_read' => true]);
    }
}
