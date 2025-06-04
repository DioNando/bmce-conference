<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __("Dashboard") }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __("Diagrammes") }}</a></li>
                <li>{{ __("Diagrammes de Séquences") }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __("Diagrammes de Séquences") }}
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
                <h2 class="card-title text-primary">{{ __("Processus métier principaux") }}</h2>
                <p class="text-base-content/70 mb-4">
                    Ces diagrammes illustrent les flux de traitement des principales fonctionnalités du système BMCE Invest.
                    Ils montrent les interactions entre les acteurs et les composants techniques pour les processus de création de meetings et réservation.
                    Ces séquences documentent le comportement dynamique du système lors des opérations critiques.
                </p>
            </div>
        </div>

        <x-diagram.mermaid
            title="Processus de création d'un meeting"
            description="Ce diagramme de séquence illustre le processus complet de création d'un meeting par un administrateur. Il montre les interactions temporelles entre l'administrateur, le système, le service d'email et les investisseurs, depuis la création initiale jusqu'à la confirmation de participation des investisseurs invités."
            definition="sequenceDiagram
                actor A as Administrateur
                participant S as Système
                participant E as Email Service
                participant I as Investisseurs

                A->>S: Crée un nouveau meeting
                S->>S: Valide les données du meeting
                S->>A: Confirmation de création
                A->>S: Ajoute des investisseurs au meeting
                S->>E: Envoie des invitations
                E->>I: Réception des invitations
                I->>S: Confirmation de participation
                S->>A: Mise à jour du statut des participants
            "
        />

        <x-diagram.mermaid
            title="Réservation d'un créneau horaire"
            description="Ce diagramme montre le processus de réservation d'un créneau horaire par un investisseur. Il détaille les étapes de consultation des meetings disponibles, sélection d'un meeting, choix d'un créneau, vérification de disponibilité et confirmation de la réservation avec notification à l'émetteur."
            definition="sequenceDiagram
                actor Inv as Investisseur
                participant S as Système
                participant Em as Émetteur

                Inv->>S: Consulte les meetings disponibles
                S->>Inv: Affiche la liste des meetings
                Inv->>S: Sélectionne un meeting
                S->>Inv: Affiche les créneaux disponibles
                Inv->>S: Réserve un créneau
                S->>S: Vérifie la disponibilité
                S->>Inv: Confirme la réservation
                S->>Em: Notifie la réservation
            "
        />
    </section>
</x-app-layout>
