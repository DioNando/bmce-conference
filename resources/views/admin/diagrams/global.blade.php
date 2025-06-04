<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __("Dashboard") }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __("Diagrammes") }}</a></li>
                <li>{{ __("Vue d'ensemble du Système") }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __("Vue d'ensemble du Système") }}
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
                <h2 class="card-title text-primary">{{ __("Architecture du système BMCE Invest") }}</h2>
                <p class="text-base-content/70 mb-4">
                    Cette vue présente l'architecture globale du système BMCE Invest sous différents angles :
                    architecture technique, flux de données, et composants système. Elle offre une perspective
                    complète sur l'organisation et le fonctionnement du système dans son ensemble.
                </p>
            </div>
        </div>

        <x-diagram.mermaid
            title="Architecture Technique du Système"
            description="Ce diagramme présente l'architecture technique du système BMCE Invest en couches. Il montre les interfaces utilisateur (web, admin, mobile), les services backend (API, authentification, logique métier), les bases de données (MySQL, Redis, stockage) et les services externes (email, QR, export)."
            definition="graph TB
                subgraph Frontend[Interface Utilisateur]
                    Web[Application Web Laravel]
                    Admin[Interface Admin]
                    Mobile[Interface Mobile]
                end

                subgraph Backend[Serveur d'Application]
                    API[API REST]
                    Auth[Authentification]
                    Business[Logique Métier]
                    Jobs[Jobs Asynchrones]
                end

                subgraph Database[Base de Données]
                    MySQL[(Base MySQL)]
                    Redis[(Cache Redis)]
                    Files[Stockage Fichiers]
                end

                subgraph External[Services Externes]
                    Email[Service Email]
                    QR[Générateur QR]
                    Export[Export Excel/PDF]
                end

                Web --> API
                Admin --> API
                Mobile --> API

                API --> Auth
                API --> Business
                API --> Jobs

                Auth --> MySQL
                Business --> MySQL
                Business --> Redis
                Jobs --> MySQL

                Business --> Email
                Business --> QR
                Business --> Export

                Files --> MySQL

                classDef frontend fill:#e3f2fd,stroke:#1976d2,stroke-width:2px
                classDef backend fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
                classDef database fill:#e8f5e8,stroke:#388e3c,stroke-width:2px
                classDef external fill:#fff3e0,stroke:#f57c00,stroke-width:2px

                class Web,Admin,Mobile frontend
                class API,Auth,Business,Jobs backend
                class MySQL,Redis,Files database
                class Email,QR,Export external
            "
        />

        <x-diagram.mermaid
            title="Flux de Données Principales"
            description="Ce diagramme de séquence illustre les flux de données principaux du système pour trois processus clés : l'inscription et connexion des utilisateurs, la gestion des meetings, et la réservation de créneaux. Il montre les interactions entre l'utilisateur, l'interface web, l'API, la base de données et les services externes."
            definition="sequenceDiagram
                participant U as Utilisateur
                participant W as Interface Web
                participant A as API
                participant DB as Base de Données
                participant E as Services Externes

                Note over U,E: Processus d'inscription et connexion
                U->>W: Accès au système
                W->>A: Demande d'authentification
                A->>DB: Vérification utilisateur
                DB-->>A: Données utilisateur
                A-->>W: Token d'authentification
                W-->>U: Accès accordé

                Note over U,E: Gestion des meetings
                U->>W: Création meeting
                W->>A: Données meeting
                A->>DB: Enregistrement
                A->>E: Envoi invitations
                E-->>A: Confirmation envoi
                A-->>W: Meeting créé
                W-->>U: Confirmation

                Note over U,E: Réservation créneaux
                U->>W: Réservation créneau
                W->>A: Demande réservation
                A->>DB: Vérification disponibilité
                DB-->>A: Statut créneau
                A->>DB: Réservation
                A->>E: Notification
                A-->>W: Confirmation
                W-->>U: Créneau réservé
            "
        />

        <x-diagram.mermaid
            title="Composants du Système"
            description="Ce diagramme présente l'organisation modulaire du système BMCE Invest en quatre grandes catégories : gestion des utilisateurs, gestion des meetings, fonctionnalités système et couche de données. Il montre les relations et dépendances entre les différents composants du système."
            definition="graph LR
                subgraph UserMgmt[Gestion Utilisateurs]
                    Users[Utilisateurs]
                    Orgs[Organisations]
                    Roles[Rôles & Permissions]
                    Auth[Authentification]
                end

                subgraph MeetingMgmt[Gestion Meetings]
                    Meetings[Meetings]
                    Rooms[Salles]
                    Slots[Créneaux]
                    Invites[Invitations]
                end

                subgraph SystemFeatures[Fonctionnalités Système]
                    QRGen[Génération QR]
                    Exports[Exports Excel/PDF]
                    Notifications[Notifications]
                    Scanner[Scanner QR]
                end

                subgraph DataLayer[Couche Données]
                    Database[(Base de Données)]
                    Cache[(Cache)]
                    Storage[(Stockage)]
                end

                Users --> Auth
                Orgs --> Users
                Roles --> Users

                Meetings --> Rooms
                Meetings --> Slots
                Meetings --> Invites
                Invites --> Users

                QRGen --> Meetings
                Exports --> Meetings
                Exports --> Users
                Notifications --> Invites
                Scanner --> QRGen

                UserMgmt --> DataLayer
                MeetingMgmt --> DataLayer
                SystemFeatures --> DataLayer

                classDef userClass fill:#e1f5fe,stroke:#01579b,stroke-width:2px
                classDef meetingClass fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
                classDef systemClass fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
                classDef dataClass fill:#fff3e0,stroke:#e65100,stroke-width:2px

                class Users,Orgs,Roles,Auth userClass
                class Meetings,Rooms,Slots,Invites meetingClass
                class QRGen,Exports,Notifications,Scanner systemClass
                class Database,Cache,Storage dataClass
            "
        />
    </section>
</x-app-layout>
