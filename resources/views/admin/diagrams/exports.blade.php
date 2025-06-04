<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __("Dashboard") }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __("Diagrammes") }}</a></li>
                <li>{{ __("Export de Données") }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __("Processus d'Export de Données") }}
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
                <h2 class="card-title text-primary">{{ __("Système d'export Excel en arrière-plan") }}</h2>
                <p class="text-base-content/70 mb-4">
                    Ce diagramme montre le processus d'export de données vers Excel en utilisant Laravel Excel et les jobs en arrière-plan.
                    Il inclut la gestion des exports volumineux avec notification par email une fois le fichier prêt au téléchargement.
                    Le système optimise les performances en traitant les exports de façon asynchrone pour ne pas bloquer l'interface utilisateur.
                </p>
            </div>
        </div>

        <x-diagram.mermaid
            title="Export Excel avec Jobs en Arrière-plan"
            description="Ce diagramme de séquence détaille le processus complet d'export de données vers Excel avec gestion asynchrone. Il couvre la demande d'export, la validation des paramètres, la mise en queue du job, le traitement en arrière-plan, la génération du fichier, la notification par email et le téléchargement. Le système gère aussi le nettoyage automatique des fichiers expirés."
            definition="sequenceDiagram
                participant U as Utilisateur
                participant EC as ExportController
                participant EV as ExportValidator
                participant QS as QueueService
                participant EJ as ExportJob
                parameter LE as LaravelExcel
                participant FS as FileStorage
                participant NS as NotificationService
                participant ES as EmailService
                participant DB as Base de Données
                participant Redis as Queue/Redis

                Note over U,Redis: Processus d'Export de Données BMCE Invest

                %% Phase 1: Demande d'export
                U->>EC: GET /admin/exports (page d'export)
                EC->>EC: Vérifier permissions (export_data)
                EC->>U: Formulaire sélection données à exporter

                %% Phase 2: Configuration de l'export
                U->>EC: POST /admin/exports/generate {type, filters, format}
                EC->>EV: Valider paramètres export

                %% Phase 3: Validation des paramètres
                EV->>EV: Valider type export (users, meetings, organizations, etc.)
                EV->>EV: Valider filtres (dates, statuts, etc.)
                EV->>EV: Valider format (xlsx, csv, pdf)
                EV->>EV: Vérifier autorisations données demandées

                alt Paramètres invalides
                    EV->>EC: Erreurs de validation
                    EC->>U: Retour formulaire avec erreurs
                else Paramètres valides
                    EV->>EC: Validation réussie

                    %% Phase 4: Estimation de la taille
                    EC->>DB: Estimer nombre d'enregistrements
                    DB->>EC: Nombre estimé

                    alt Export petit (< 1000 lignes)
                        %% Phase 5a: Export synchrone pour petits volumes
                        EC->>LE: Générer fichier immédiatement
                        LE->>DB: Récupérer données avec pagination
                        DB->>LE: Résultats paginés
                        LE->>LE: Construire fichier Excel/CSV
                        LE->>FS: Sauvegarder fichier temporaire
                        FS->>EC: Chemin fichier généré
                        EC->>U: Téléchargement direct du fichier

                    else Export volumineux (>= 1000 lignes)
                        %% Phase 5b: Export asynchrone pour gros volumes
                        EC->>DB: Créer entrée export_jobs
                        DB->>EC: ID du job d'export

                        %% Phase 6: Mise en queue du job
                        EC->>QS: Dispatcher ExportJob
                        QS->>Redis: Ajouter job à la queue 'exports'
                        Redis->>QS: Job mis en queue
                        QS->>EC: Job ID généré

                        %% Phase 7: Réponse immédiate à l'utilisateur
                        EC->>U: 'Export en cours, vous recevrez un email'

                        %% Phase 8: Traitement asynchrone
                        Redis->>EJ: Démarrer traitement du job
                        EJ->>DB: Mettre à jour statut 'processing'

                        %% Phase 9: Génération du fichier en arrière-plan
                        EJ->>DB: Récupérer données par chunks

                        loop Traitement par chunks de 1000
                            EJ->>DB: SELECT * FROM table LIMIT 1000 OFFSET x
                            DB->>EJ: Chunk de données
                            EJ->>LE: Ajouter chunk au fichier Excel
                            LE->>LE: Traiter et formatter chunk
                            EJ->>DB: Mettre à jour progression (%)
                        end

                        %% Phase 10: Finalisation du fichier
                        EJ->>LE: Finaliser fichier Excel
                        LE->>FS: Sauvegarder fichier dans storage/exports
                        FS->>EJ: Chemin final du fichier

                        %% Phase 11: Mise à jour statut
                        EJ->>DB: Mettre à jour statut 'completed'
                        EJ->>DB: Enregistrer chemin fichier et taille

                        %% Phase 12: Notification par email
                        EJ->>NS: Déclencher notification export terminé
                        NS->>ES: Préparer email de notification
                        ES->>ES: Construire email avec lien téléchargement
                        ES->>U: Envoyer email 'Export terminé'

                        %% Phase 13: Logs et nettoyage
                        EJ->>DB: Log completion du job
                        EJ->>EJ: Planifier suppression auto du fichier (7 jours)
                    end
                end

                Note over U,Redis: Export terminé et fichier disponible

                %% Phase 14: Téléchargement du fichier
                U->>EC: GET /admin/exports/{id}/download (depuis email)
                EC->>DB: Vérifier existence et droits sur le fichier

                alt Fichier non trouvé ou expiré
                    EC->>U: Erreur 404 'Fichier non disponible'
                else Fichier disponible
                    EC->>FS: Récupérer fichier depuis storage
                    FS->>EC: Contenu du fichier
                    EC->>U: Téléchargement forcé du fichier
                    EC->>DB: Log téléchargement
                end

                %% Phase 15: Gestion des erreurs
                alt Erreur pendant le traitement
                    EJ->>DB: Mettre à jour statut 'failed'
                    EJ->>DB: Enregistrer message d'erreur
                    EJ->>NS: Notifier échec export
                    NS->>ES: Email d'erreur à l'utilisateur
                    ES->>U: 'Erreur lors de l'export'
                end

                %% Phase 16: Nettoyage automatique
                Note over EJ,FS: Tâche cron quotidienne
                EJ->>DB: Récupérer exports > 7 jours
                DB->>EJ: Liste fichiers à supprimer
                EJ->>FS: Supprimer fichiers expirés
                EJ->>DB: Supprimer entrées export_jobs expirées

                Note over U,Redis: Système d'export complet avec gestion asynchrone
            "
        />
    </section>
</x-app-layout>
