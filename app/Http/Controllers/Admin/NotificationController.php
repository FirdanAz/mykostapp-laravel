<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->appNotifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) abort(403);
        $notification->markAsRead();
        if ($notification->url) return redirect($notification->url);
        return back();
    }

    public function markAllRead(): JsonResponse
    {
        auth()->user()->appNotifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function getUnread(): JsonResponse
    {
        $notifications = auth()->user()->unreadNotifications()
            ->latest()->take(10)->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'title'   => $n->title,
                'message' => $n->message,
                'icon'    => $n->icon,
                'url'     => $n->url ? route('notifications.read', $n) : '#',
                'time'    => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'count'         => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
