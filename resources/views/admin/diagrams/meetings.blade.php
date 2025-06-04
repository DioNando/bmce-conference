<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __("Dashboard") }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __("Diagrammes") }}</a></li>
                <li>{{ __("Création de Meeting") }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __("Processus de Création de Meeting") }}
            </h3>
            <div>
                <a href="{{ route('admin.diagrams.index') }}" class="btn btn-ghost rounded-full">
                    <x-heroicon-s-arrow-left class="size-4" />
                    {{ __("Retour") }}
                </a>
            </div>
        </div>
    </x-slot>

    <section class="space-y-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-primary">{{ __("Flux de création et gestion des meetings") }}</h2>
                <p class="text-base-content/70 mb-4">
                    Ce diagramme détaille le processus complet de création d'un meeting, depuis la saisie des informations jusqu'à la notification des investisseurs.
                    Il inclut la validation des disponibilités des salles, la génération automatique des créneaux horaires et la gestion des invitations.
                    Le processus assure l'intégrité des données et la cohérence des réservations dans le système.
                </p>
            </div>
        </div>

        <x-diagram.mermaid
            title="Création et Gestion des Meetings"
            description="Ce diagramme de séquence détaille le processus technique complet de création d'un meeting dans le système BMCE Invest. Il montre les interactions entre les contrôleurs, modèles, services et base de données, incluant la validation, la génération des créneaux, les invitations et les notifications automatiques."
            definition="sequenceDiagram
                participant A as Admin/Issuer
                participant MC as MeetingController
                participant MV as MeetingValidator
                participant MM as MeetingModel
                participant RM as RoomModel
                participant TS as TimeSlotService
                participant NS as NotificationService
                participant ES as EmailService
                participant IM as InvestorModel
                participant MIM as MeetingInvestorModel
                participant DB as Base de Données

                Note over A,DB: Processus de Création de Meeting BMCE Invest

                %% Phase 1: Accès au formulaire de création
                A->>MC: GET /admin/meetings/create
                MC->>MC: Vérifier permissions (create_meetings)
                MC->>RM: Récupérer salles disponibles
                RM->>DB: SELECT * FROM rooms WHERE is_active = true
                DB->>RM: Liste des salles actives
                RM->>MC: Collection salles
                MC->>A: Formulaire création + salles disponibles

                %% Phase 2: Soumission du formulaire
                A->>MC: POST /admin/meetings {title, description, dates, room_id, etc.}
                MC->>MV: Valider les données du formulaire

                %% Phase 3: Validation des données
                MV->>MV: Valider title (required, max:255)
                MV->>MV: Valider description (required)
                MV->>MV: Valider start_date >= today
                MV->>MV: Valider end_date >= start_date
                MV->>MV: Valider heures (start_time < end_time)
                MV->>MV: Valider time_slot_duration (15,30,45,60 min)
                MV->>MV: Valider max_participants > 0

                alt Données invalides
                    MV->>MC: Erreurs de validation
                    MC->>A: Retour formulaire avec erreurs
                else Données valides
                    MV->>MC: Validation réussie

                    %% Phase 4: Vérification disponibilité salle
                    MC->>RM: checkAvailability(room_id, start_date, end_date, times)
                    RM->>DB: Vérifier conflits avec meetings existants
                    DB->>RM: Résultat vérification

                    alt Salle non disponible
                        RM->>MC: Conflit détecté
                        MC->>A: Erreur 'Salle non disponible aux dates sélectionnées'
                    else Salle disponible
                        RM->>MC: Disponibilité confirmée

                        %% Phase 5: Début transaction base de données
                        MC->>DB: BEGIN TRANSACTION

                        %% Phase 6: Création du meeting
                        MC->>MM: Créer nouveau meeting
                        MM->>DB: INSERT INTO meetings (...)
                        DB->>MM: Meeting créé avec ID
                        MM->>MC: Objet Meeting créé

                        %% Phase 7: Génération des créneaux horaires
                        MC->>TS: generateTimeSlots(meeting, room, dates, duration)
                        TS->>TS: Calculer tous les créneaux possibles

                        loop Pour chaque jour du meeting
                            TS->>TS: Générer créneaux de start_time à end_time
                            TS->>DB: INSERT INTO time_slots (meeting_id, start_time, end_time, status='available')
                        end

                        TS->>MC: Créneaux générés

                        %% Phase 8: Sélection des investisseurs (optionnel)
                        alt Sélection d'investisseurs lors de la création
                            MC->>A: Formulaire sélection investisseurs
                            A->>MC: POST {selected_investors[]}
                            MC->>IM: Récupérer investisseurs sélectionnés
                            IM->>DB: SELECT organizations WHERE profil='investor' AND id IN (...)
                            DB->>IM: Liste investisseurs
                            IM->>MC: Collection investisseurs

                            %% Associer investisseurs au meeting
                            loop Pour chaque investisseur sélectionné
                                MC->>MIM: Créer association meeting-investisseur
                                MIM->>DB: INSERT INTO meeting_investors (meeting_id, organization_id, status='invited')
                            end
                        end

                        %% Phase 9: Finalisation et commit
                        MC->>DB: COMMIT TRANSACTION

                        %% Phase 10: Notifications et emails
                        MC->>NS: Déclencher notifications
                        NS->>ES: Préparer emails d'invitation

                        alt Meeting avec investisseurs assignés
                            loop Pour chaque investisseur invité
                                ES->>ES: Générer email invitation personnalisé
                                ES->>ES: Envoyer email (queue job)
                                ES->>DB: Log email envoyé
                            end
                        end

                        %% Phase 11: Logs et audit
                        MC->>DB: Log création meeting (audit trail)

                        %% Phase 12: Redirection succès
                        MC->>A: Redirection vers meeting créé + message succès
                    end
                end

                Note over A,DB: Meeting créé et prêt pour les réservations

                %% Phase 13: Publication du meeting (étape séparée)
                A->>MC: POST /admin/meetings/{id}/publish
                MC->>MM: Mettre à jour is_published = true
                MM->>DB: UPDATE meetings SET is_published = true
                MC->>NS: Notifier publication aux investisseurs
                NS->>ES: Envoyer emails de disponibilité

                loop Pour chaque investisseur associé
                    ES->>ES: Email 'Meeting disponible pour réservation'
                    ES->>ES: Inclure lien vers calendrier de réservation
                end

                MC->>A: Confirmation publication

                Note over A,DB: Meeting publié et ouvert aux réservations
            "
        />
    </section>
</x-app-layout>
