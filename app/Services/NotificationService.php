<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Créer une notification pour un utilisateur.
     *
     * @param int $userId ID de l'utilisateur
     * @param string $title Titre de la notification
     * @param string $message Corps de la notification
     * @param string $type Type de notification (meeting, question, system)
     * @param Model|null $related Modèle associé à la notification
     * @param array $data Données supplémentaires (optionnel)
     * @return Notification
     */
    public function create(int $userId, string $title, string $message, string $type = 'info', ?Model $related = null, array $data = [])
    {
        $notification = new Notification([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
        ]);

        if ($related) {
            $notification->related_id = $related->id;
            $notification->related_type = get_class($related);
        }

        $notification->save();

        return $notification;
    }

    /**
     * Créer une notification de meeting.
     *
     * @param int $userId
     * @param \App\Models\Meeting $meeting
     * @param string $title
     * @param string $message
     * @param array $data
     * @return Notification
     */
    public function createMeetingNotification(int $userId, Meeting $meeting, string $title, string $message, array $data = [])
    {
        return $this->create($userId, $title, $message, 'meeting', $meeting, $data);
    }

    /**
     * Notifier tous les investisseurs d'un meeting.
     *
     * @param \App\Models\Meeting $meeting
     * @param string $title
     * @param string $message
     * @return array Notifications créées
     */
    public function notifyMeetingInvestors(Meeting $meeting, string $title, string $message)
    {
        $notifications = [];

        foreach ($meeting->investors as $investor) {
            $notifications[] = $this->createMeetingNotification(
                $investor->id,
                $meeting,
                $title,
                $message,
                ['meeting_id' => $meeting->id]
                );
        }

        return $notifications;
    }
}
