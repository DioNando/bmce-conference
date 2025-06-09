<?php

namespace App\Observers;

use App\Models\Meeting;
use App\Services\NotificationService;

class MeetingObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Meeting "created" event.
     */
    public function created(Meeting $meeting): void
    {
        // Notifier l'émetteur qu'un nouveau meeting a été créé
        if ($meeting->issuer_id !== $meeting->created_by_id) {
            $this->notificationService->createMeetingNotification(
                $meeting->issuer_id,
                $meeting,
                'Nouveau meeting planifié',
                "Un meeting a été planifié avec vous le {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')}."
            );
        }
    }

    /**
     * Handle the Meeting "updated" event.
     */
    public function updated(Meeting $meeting): void
    {
        // Si le statut a changé
        if ($meeting->isDirty('status')) {
            $statusLabel = $meeting->status->label();

            // Notifier l'émetteur du changement de statut
            $this->notificationService->createMeetingNotification(
                $meeting->issuer_id,
                $meeting,
                'Statut du meeting mis à jour',
                "Le statut de votre meeting du {$meeting->timeSlot->date->format('d/m/Y')} a été mis à jour en '{$statusLabel}'."
            );

            // Notifier tous les investisseurs associés
            $this->notificationService->notifyMeetingInvestors(
                $meeting,
                'Statut du meeting mis à jour',
                "Le statut du meeting avec {$meeting->issuer->organization->name} le {$meeting->timeSlot->date->format('d/m/Y')} a été mis à jour en '{$statusLabel}'."
            );
        }

        // Si le créneau a changé
        if ($meeting->isDirty('time_slot_id')) {
            // Obtenir l'ancien et le nouveau créneau
            $oldTimeSlot = $meeting->getOriginal('time_slot_id');
            $newTimeSlot = $meeting->time_slot_id;

            if ($oldTimeSlot != $newTimeSlot) {
                // Notifier l'émetteur du changement d'horaire
                $this->notificationService->createMeetingNotification(
                    $meeting->issuer_id,
                    $meeting,
                    'Horaire du meeting modifié',
                    "L'horaire de votre meeting a été modifié. Nouvelle date : {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')}."
                );

                // Notifier tous les investisseurs associés
                $this->notificationService->notifyMeetingInvestors(
                    $meeting,
                    'Horaire du meeting modifié',
                    "L'horaire du meeting avec {$meeting->issuer->organization->name} a été modifié. Nouvelle date : {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')}."
                );
            }
        }

        // Si la salle a changé
        if ($meeting->isDirty('room_id')) {
            $roomName = $meeting->room ? $meeting->room->name : 'Aucune salle';

            // Notifier l'émetteur du changement de salle
            $this->notificationService->createMeetingNotification(
                $meeting->issuer_id,
                $meeting,
                'Salle du meeting modifiée',
                "La salle de votre meeting du {$meeting->timeSlot->date->format('d/m/Y')} a été modifiée. Nouvelle salle : {$roomName}."
            );

            // Notifier tous les investisseurs associés
            $this->notificationService->notifyMeetingInvestors(
                $meeting,
                'Salle du meeting modifiée',
                "La salle du meeting avec {$meeting->issuer->organization->name} le {$meeting->timeSlot->date->format('d/m/Y')} a été modifiée. Nouvelle salle : {$roomName}."
            );
        }
    }

    /**
     * Handle the Meeting "deleted" event.
     */
    public function deleted(Meeting $meeting): void
    {
        // Notifier l'émetteur de la suppression
        $this->notificationService->createMeetingNotification(
            $meeting->issuer_id,
            $meeting,
            'Meeting annulé',
            "Votre meeting du {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')} a été annulé."
        );

        // Notifier tous les investisseurs associés
        $this->notificationService->notifyMeetingInvestors(
            $meeting,
            'Meeting annulé',
            "Le meeting avec {$meeting->issuer->organization->name} prévu le {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')} a été annulé."
        );
    }
}
