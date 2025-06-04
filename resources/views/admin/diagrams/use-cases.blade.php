<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __("Dashboard") }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __("Diagrammes") }}</a></li>
                <li>{{ __("Diagrammes de Cas d'Utilisation") }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __("Diagrammes de Cas d'Utilisation") }}
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
        <x-diagram.mermaid
            title="Cas d'Utilisation Globale - Système BMCE Invest"
            description="Ce diagramme présente une vue d'ensemble des interactions entre les différents acteurs du système BMCE Invest. Il illustre les principales fonctionnalités accessibles selon les rôles : administration du système, création et gestion des meetings, participation des investisseurs, et accès des visiteurs."
            definition="graph TD
                Admin((Administrateur))
                Issuer((Émetteur))
                Investor((Investisseur))
                Guest((Visiteur))

                %% Gestion des utilisateurs
                ManageUsers[Gérer les utilisateurs]
                ManageOrgs[Gérer les organisations]
                ManagePerms[Gérer les permissions]

                %% Gestion des meetings
                CreateMeeting[Créer un meeting]
                ManageMeetings[Gérer les meetings]
                ScheduleMeeting[Planifier un meeting]

                %% Participation aux meetings
                JoinMeeting[Participer à un meeting]
                BookSlot[Réserver un créneau]
                ReceiveInvite[Recevoir une invitation]

                %% Gestion du profil
                ManageProfile[Gérer son profil]
                ViewDashboard[Consulter le tableau de bord]

                %% Accès système
                Login[Se connecter]
                Register[S'inscrire]
                ResetPassword[Réinitialiser mot de passe]

                %% Exportation et rapports
                ExportData[Exporter des données]
                ViewReports[Consulter les rapports]
                GenerateQR[Générer codes QR]
                ScanUsers[Scanner les utilisateurs]

                %% Relations Administrateur
                Admin --> ManageUsers
                Admin --> ManageOrgs
                Admin --> ManagePerms
                Admin --> ManageMeetings
                Admin --> ExportData
                Admin --> ViewReports
                Admin --> GenerateQR
                Admin --> ScanUsers
                Admin --> ViewDashboard
                Admin --> ManageProfile

                %% Relations Émetteur
                Issuer --> CreateMeeting
                Issuer --> ScheduleMeeting
                Issuer --> ManageProfile
                Issuer --> ViewDashboard
                Issuer --> Login

                %% Relations Investisseur
                Investor --> JoinMeeting
                Investor --> BookSlot
                Investor --> ReceiveInvite
                Investor --> ManageProfile
                Investor --> ViewDashboard
                Investor --> Login

                %% Relations Visiteur
                Guest --> Register
                Guest --> Login
                Guest --> ResetPassword

                %% Dépendances
                Register --> Login
                Login --> ViewDashboard
                ReceiveInvite --> BookSlot
                CreateMeeting --> ScheduleMeeting
                ManageUsers --> ManagePerms
            "
        />

        <x-diagram.mermaid
            title="Cas d'Utilisation - Gestion des Utilisateurs"
            description="Ce diagramme détaille les cas d'utilisation spécifiques à la gestion des utilisateurs par l'administrateur. Il couvre la création, modification, suppression des comptes, ainsi que l'assignation des rôles et la gestion des permissions au sein du système."
            definition="graph TD
                Admin((Administrateur))
                CreateUser[Créer un utilisateur]
                EditUser[Modifier un utilisateur]
                DeleteUser[Supprimer un utilisateur]
                AssignRole[Assigner un rôle]
                ManagePermissions[Gérer les permissions]

                Admin --> CreateUser
                Admin --> EditUser
                Admin --> DeleteUser
                Admin --> AssignRole
                Admin --> ManagePermissions

                AssignRole --> CreateUser
                ManagePermissions --> AssignRole
            "
        />

        <x-diagram.mermaid
            title="Cas d'Utilisation - Gestion des Meetings"
            description="Ce diagramme illustre le processus complet de gestion des meetings, depuis leur création par les administrateurs et émetteurs jusqu'à la participation des investisseurs. Il montre les étapes d'invitation, d'acceptation/refus, et de réservation des créneaux horaires."
            definition="graph TD
                Admin((Administrateur))
                Issuer((Émetteur))
                Investor((Investisseur))

                CreateMeeting[Créer un meeting]
                ScheduleMeeting[Planifier un meeting]
                InviteInvestors[Inviter des investisseurs]
                AcceptInvite[Accepter une invitation]
                DeclineInvite[Décliner une invitation]
                BookTimeSlot[Réserver un créneau]

                Admin --> CreateMeeting
                Admin --> ScheduleMeeting
                Admin --> InviteInvestors

                Issuer --> ScheduleMeeting
                Issuer --> InviteInvestors

                Investor --> AcceptInvite
                Investor --> DeclineInvite
                Investor --> BookTimeSlot

                InviteInvestors -- précède --> AcceptInvite
                InviteInvestors -- précède --> DeclineInvite
                AcceptInvite -- précède --> BookTimeSlot
            "
        />

        <x-diagram.mermaid
            title="Cas d'Utilisation - Workflow des Investisseurs"
            description="Ce diagramme se concentre sur l'expérience utilisateur des investisseurs, détaillant leur parcours depuis la réception d'une invitation jusqu'à leur participation effective aux meetings. Il inclut les options de gestion de profil et les fonctionnalités de tableau de bord disponibles."
            definition="graph TD
                Investor((Investisseur))
                Guest((Visiteur))

                %% Processus d'inscription et connexion
                Register[S'inscrire]
                Login[Se connecter]
                ResetPassword[Réinitialiser mot de passe]

                %% Gestion du profil
                UpdateProfile[Mettre à jour le profil]
                ViewProfile[Consulter le profil]
                ChangePassword[Changer le mot de passe]

                %% Participation aux meetings
                ReceiveInvitation[Recevoir une invitation]
                ViewMeetingDetails[Consulter les détails du meeting]
                AcceptInvitation[Accepter l'invitation]
                DeclineInvitation[Décliner l'invitation]
                SelectTimeSlot[Sélectionner un créneau]
                ConfirmBooking[Confirmer la réservation]

                %% Dashboard et notifications
                ViewDashboard[Consulter le tableau de bord]
                ViewUpcomingMeetings[Voir meetings à venir]
                ViewHistory[Consulter l'historique]

                %% Relations Visiteur
                Guest --> Register
                Guest --> Login
                Guest --> ResetPassword

                %% Relations Investisseur - Authentification
                Investor --> Login
                Investor --> UpdateProfile
                Investor --> ViewProfile
                Investor --> ChangePassword
                Investor --> ViewDashboard

                %% Relations Investisseur - Meetings
                Investor --> ReceiveInvitation
                Investor --> ViewMeetingDetails
                Investor --> AcceptInvitation
                Investor --> DeclineInvitation
                Investor --> SelectTimeSlot
                Investor --> ConfirmBooking
                Investor --> ViewUpcomingMeetings
                Investor --> ViewHistory

                %% Workflow séquentiel
                Register --> Login
                Login --> ViewDashboard
                ReceiveInvitation --> ViewMeetingDetails
                ViewMeetingDetails --> AcceptInvitation
                ViewMeetingDetails --> DeclineInvitation
                AcceptInvitation --> SelectTimeSlot
                SelectTimeSlot --> ConfirmBooking
            "
        />

        <x-diagram.mermaid
            title="Cas d'Utilisation - Administration Système"
            description="Ce diagramme détaille les cas d'utilisation réservés aux administrateurs système, incluant la gestion complète des utilisateurs et organisations, l'export de données, la génération de rapports, ainsi que les fonctionnalités avancées comme la génération de codes QR et le scanning des utilisateurs."
            definition="graph TD
                Admin((Administrateur))

                %% Gestion des utilisateurs
                ManageUsers[Gérer les utilisateurs]
                CreateUser[Créer un utilisateur]
                EditUser[Modifier un utilisateur]
                DeleteUser[Supprimer un utilisateur]
                AssignRoles[Assigner des rôles]
                ManagePermissions[Gérer les permissions]

                %% Gestion des organisations
                ManageOrganizations[Gérer les organisations]
                CreateOrganization[Créer une organisation]
                EditOrganization[Modifier une organisation]
                DeleteOrganization[Supprimer une organisation]

                %% Gestion des meetings
                OverviewMeetings[Vue d'ensemble des meetings]
                ApproveMeetings[Approuver les meetings]
                CancelMeetings[Annuler les meetings]
                ModifyMeetings[Modifier les meetings]

                %% Rapports et exports
                GenerateReports[Générer des rapports]
                ExportUserData[Exporter données utilisateurs]
                ExportMeetingData[Exporter données meetings]
                AnalyticsView[Consulter les analytics]

                %% Fonctionnalités QR
                GenerateQRCodes[Générer codes QR]
                ScanUsers[Scanner les utilisateurs]
                ManageQRAccess[Gérer accès QR]

                %% Relations principales
                Admin --> ManageUsers
                Admin --> ManageOrganizations
                Admin --> OverviewMeetings
                Admin --> GenerateReports
                Admin --> GenerateQRCodes

                %% Relations détaillées - Utilisateurs
                ManageUsers --> CreateUser
                ManageUsers --> EditUser
                ManageUsers --> DeleteUser
                ManageUsers --> AssignRoles
                ManageUsers --> ManagePermissions

                %% Relations détaillées - Organisations
                ManageOrganizations --> CreateOrganization
                ManageOrganizations --> EditOrganization
                ManageOrganizations --> DeleteOrganization

                %% Relations détaillées - Meetings
                OverviewMeetings --> ApproveMeetings
                OverviewMeetings --> CancelMeetings
                OverviewMeetings --> ModifyMeetings

                %% Relations détaillées - Rapports
                GenerateReports --> ExportUserData
                GenerateReports --> ExportMeetingData
                GenerateReports --> AnalyticsView

                %% Relations détaillées - QR
                GenerateQRCodes --> ScanUsers
                GenerateQRCodes --> ManageQRAccess
            "
        />
    </section>
</x-app-layout>
