<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationTestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Génère quelques notifications de test
     */
    public function generateTestNotifications()
    {
        $user = Auth::user();

        // Création d'une notification basique
        $this->notificationService->create(
            $user->id,
            'Notification de test',
            'Ceci est une notification de test générée manuellement.',
            'system'
        );

        // Si des meetings existent, créer des notifications liées à un meeting
        $meetings = Meeting::take(2)->get();

        if ($meetings->isNotEmpty()) {
            foreach ($meetings as $meeting) {
                $this->notificationService->createMeetingNotification(
                    $user->id,
                    $meeting,
                    'Nouveau meeting de test',
                    "Un meeting de test a été créé le {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')}."
                );
            }
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notifications de test générées avec succès!');
    }
}
