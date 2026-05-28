<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at?->toDateTimeString(),
                'created_at' => $notification->created_at?->toDateTimeString(),
            ]);

        return response()->json([
            'message' => 'Notificaciones obtenidas correctamente',
            'data' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'unread_messages_count' => $this->unreadMessagesCount($request),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'unread_messages_count' => $this->unreadMessagesCount($request),
            ],
        ]);
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notificación marcada como leída',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Notificaciones marcadas como leídas',
        ]);
    }

    private function unreadMessagesCount(Request $request): int
    {
        $user = $request->user();

        return $user->conversations()
            ->get()
            ->sum(function ($conversation) use ($user) {
                $lastReadAt = $conversation->pivot?->last_read_at;

                return $conversation->messages()
                    ->where('sender_id', '!=', $user->id)
                    ->when($lastReadAt, function ($query) use ($lastReadAt) {
                        $query->where('created_at', '>', $lastReadAt);
                    })
                    ->count();
            });
    }
}
