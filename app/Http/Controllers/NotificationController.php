<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Affiche la page de toutes les notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marque une notification comme lue.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marque une notification comme non lue.
     */
    public function markAsUnread($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsUnread();

        return redirect()->back()->with('success', 'Notification marquée comme non lue.');
    }

    /**
     * Marque toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Supprime une notification.
     */
    public function delete($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notification supprimée.');
    }

    /**
     * Supprime toutes les notifications lues.
     */
    public function deleteAllRead()
    {
        Auth::user()->notifications()->whereNotNull('read_at')->delete();

        return redirect()->back()->with('success', 'Toutes les notifications lues ont été supprimées.');
    }
}
