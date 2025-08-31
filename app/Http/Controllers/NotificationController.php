<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = $this->notificationService->getUnreadNotifications(Auth::id());
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = \App\Models\Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());
        return response()->json(['success' => true]);
    }

    public function getUnreadNotifications()
    {
        $notifications = $this->notificationService->getUnreadNotifications(Auth::id());
        return response()->json($notifications);
    }

    public function getUnreadCount()
    {
        $count = $this->notificationService->getUnreadCount(Auth::id());
        return response()->json(['count' => $count]);
    }
}
