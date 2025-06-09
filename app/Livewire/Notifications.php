<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public function getCount()
    {
        return Notification::where('user_id', Auth::id())->whereNull('read_at')->count();
    }

    public function getNotifications()
    {
        return Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                    'read' => $notification->read_at !== null,
                    'action_url' => $this->getActionUrl($notification),
                    'action_text' => 'Voir détails',
                    'avatar' => $this->getAvatarForType($notification->type),
                ];
            });
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->find($id);
        
        if ($notification) {
            if ($notification->read_at) {
                $notification->update(['read_at' => null]);
            } else {
                $notification->update(['read_at' => now()]);
            }
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    protected function getActionUrl($notification)
    {
        if ($notification->related_type === 'App\Models\Meeting') {
            $roles = Auth::user()->roles->pluck('name')->toArray();
            
            if (in_array('admin', $roles)) {
                return route('admin.meetings.show', $notification->related_id);
            } elseif (in_array('issuer', $roles)) {
                return route('issuer.meetings.show', $notification->related_id);
            } elseif (in_array('investor', $roles)) {
                return route('investor.meetings.show', $notification->related_id);
            }
        }
        
        return route('notifications.index');
    }

    protected function getAvatarForType($type)
    {
        return match($type) {
            'meeting' => 'https://ui-avatars.com/api/?name=Meeting&background=2563eb&color=fff',
            'question' => 'https://ui-avatars.com/api/?name=Question&background=f59e0b&color=fff',
            'system' => 'https://ui-avatars.com/api/?name=System&background=10b981&color=fff',
            default => 'https://ui-avatars.com/api/?name=Notification&background=random',
        };
    }

    public function render()
    {
        return view('livewire.notifications', [
            'count' => $this->getCount(),
            'notifications' => $this->getNotifications(),
        ]);
    }
}
