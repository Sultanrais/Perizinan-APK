<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('perizinan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    public function markAllAsRead()
    {
        Notification::where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi telah dihapus.');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('is_read', false)->count();

        return response()->json(['count' => $count]);
    }
}
