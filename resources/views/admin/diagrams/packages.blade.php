<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __('Diagrammes') }}</a></li>
                <li>{{ __('Diagrammes de Packages') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Diagrammes de Packages') }}
            </h3>
            <div>
                <a href="{{ route('admin.diagrams.index') }}" class="btn btn-ghost rounded-full">
                    <x-heroicon-s-arrow-left class="size-4" />
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <section class="space-y-8">
        <x-diagram.mermaid title="Architecture Globale des Packages Laravel"
            description="Ce diagramme présente l'organisation globale des packages et composants du système BMCE Invest selon l'architecture Laravel. Il montre les relations entre les différentes couches : Core (modèles, contrôleurs), Business Logic (services, observateurs), Data Management (enums, exports), User Interface (vues, Livewire) et Communication (mail, requêtes)."
            definition='flowchart TB
                %% Core Laravel
                App[App Core]

                %% Main Package Categories
                subgraph Core ["Core Packages"]
                    Models["Models"]
                    Controllers["Controllers"]
                    Middlewares["Middlewares"]
                    Providers["Providers"]
                end

                subgraph Business ["Business Logic"]
                    Services["Services"]
                    Observers["Observers"]
                    Exceptions["Exceptions"]
                end

                subgraph Data ["Data Management"]
                    Enums["Enums"]
                    Exports["Exports"]
                    Imports["Imports"]
                end

                subgraph UI ["User Interface"]
                    Views["Views"]
                    Livewire["Livewire"]
                    ViewComp["View Components"]
                end

                subgraph Communication ["Communication"]
                    Mail["Mail"]
                    Requests["Requests"]
                    Console["Console"]
                end

                %% Relationships
                App --> Core
                App --> Business
                App --> Data
                App --> UI
                App --> Communication

                Core --> Business
                Business --> Data
                UI --> Core
                Communication --> Core

                classDef coreStyle fill:#e1f5fe,stroke:#01579b,stroke-width:2px
                classDef businessStyle fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
                classDef dataStyle fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
                classDef uiStyle fill:#fff3e0,stroke:#e65100,stroke-width:2px
                classDef commStyle fill:#fce4ec,stroke:#880e4f,stroke-width:2px

                class Core coreStyle
                class Business businessStyle
                class Data dataStyle
                class UI uiStyle
                class Communication commStyle
            ' />

        <x-diagram.mermaid title="Structure Détaillée des Models et Controllers"
            description="Ce diagramme détaille la structure interne des packages Models et Controllers du système BMCE Invest. Il montre les modèles Eloquent principaux (User, Meeting, Organization, etc.) et les contrôleurs correspondants, illustrant l'architecture MVC et les relations entre les entités métier."
            definition='flowchart TB
                %% Models Section
                subgraph Models ["Models Package"]
                    User["User.php"]
                    Meeting["Meeting.php"]
                    MeetingInvestor["MeetingInvestor.php"]
                    Organization["Organization.php"]
                    TimeSlot["TimeSlot.php"]
                    Room["Room.php"]
                    Question["Question.php"]
                    Event["Event.php"]
                    Country["Country.php"]
                end

                %% Controllers Section
                subgraph Controllers ["Controllers Package"]
                    subgraph AdminControllers ["Admin Controllers"]
                        AdminDash["DashboardController"]
                        AdminUser["UserController"]
                        AdminMeeting["MeetingController"]
                        AdminOrg["OrganizationController"]
                        AdminRoom["RoomController"]
                        AdminDiagram["DiagramController"]
                        AdminTimeSlot["TimeSlotController"]
                        AdminSchedule["IssuerScheduleController"]
                        AdminAdmin["AdministratorController"]
                    end

                    subgraph InvestorControllers ["Investor Controllers"]
                        InvestorDash["DashboardController"]
                        InvestorMeeting["MeetingController"]
                        InvestorIssuer["IssuerController"]
                        InvestorQuestion["QuestionController"]
                        InvestorQr["QrCodeController"]
                    end

                    subgraph IssuerControllers ["Issuer Controllers"]
                        IssuerDash["DashboardController"]
                        IssuerSchedule["ScheduleController"]
                        IssuerMeeting["MeetingController"]
                        IssuerQuestion["QuestionController"]
                    end

                    subgraph SharedControllers ["Shared Controllers"]
                        Profile["ProfileController"]
                        Language["LanguageController"]
                        MainMeeting["MeetingController"]
                        MainQuestion["QuestionController"]
                        Home["HomeController"]
                    end
                end

                %% Relationships
                Models --> Controllers
                User --> AdminUser
                User --> InvestorDash
                User --> IssuerDash
                Meeting --> AdminMeeting
                Meeting --> InvestorMeeting
                Meeting --> IssuerMeeting
                Organization --> AdminOrg
                Room --> AdminRoom
                TimeSlot --> AdminTimeSlot
                Question --> InvestorQuestion
                Question --> IssuerQuestion

                classDef modelStyle fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
                classDef adminStyle fill:#e1f5fe,stroke:#01579b,stroke-width:2px
                classDef investorStyle fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
                classDef issuerStyle fill:#fff3e0,stroke:#e65100,stroke-width:2px
                classDef sharedStyle fill:#fce4ec,stroke:#880e4f,stroke-width:2px

                class Models modelStyle
                class AdminControllers adminStyle
                class InvestorControllers investorStyle
                class IssuerControllers issuerStyle
                class SharedControllers sharedStyle

            '
        />

        <x-diagram.mermaid
            title="Enums, Exports et Services Métier"
            description="Ce diagramme présente les packages utilitaires du système BMCE Invest : les Enums qui définissent les constantes métier (statuts, rôles, types), les classes d'Export pour les données Excel/PDF, et les Services métier qui encapsulent la logique complexe. Ces composants assurent la cohérence et la réutilisabilité du code."
            definition='flowchart TB
                %% Enums Section
                subgraph Enums ["Enums Package"]
                    UserRole["UserRole.php<br/>ADMIN, ISSUER, INVESTOR"]
                    MeetingStatus["MeetingStatus.php<br/>PENDING, CONFIRMED, CANCELLED"]
                    InvestorStatus["InvestorStatus.php<br/>PENDING, CONFIRMED, REFUSED"]
                    OrganizationType["OrganizationType.php<br/>BANK, FUND, CORPORATION"]
                    Status["Status.php<br/>ACTIVE, INACTIVE"]
                    Origin["Origin.php<br/>LOCAL, INTERNATIONAL"]
                end

                %% Exports Section
                subgraph Exports ["Exports Package"]
                    UsersExport["UsersExport.php"]
                    MeetingsExport["MeetingsExport.php"]
                    MeetingInvestorsExport["MeetingInvestorsExport.php"]
                    OrganizationsExport["OrganizationsExport.php"]
                    QrCodeExport["QrCodeExport.php"]
                end

                %% Imports Section
                subgraph Imports ["Imports Package"]
                    UsersImport["UsersImport.php"]
                    OrganizationsImport["OrganizationsImport.php"]
                end

                %% Services Section
                subgraph Services ["Services Package"]
                    DashboardCache["DashboardCacheService.php"]
                    EmailService["Email Service (Mail)"]
                    ReportService["Report Service (Exports)"]
                    NotificationService["Notification Service"]
                end

                %% Support Packages
                subgraph Support ["Support Packages"]
                    subgraph Mail ["Mail"]
                        ActivationEmail["ActivationEmail.php"]
                        MeetingInvitation["MeetingInvitation.php"]
                    end

                    subgraph Middlewares ["Middlewares"]
                        RoleMiddleware["RoleMiddleware.php"]
                        SetLocaleMiddleware["SetLocaleMiddleware.php"]
                    end

                    subgraph Observers ["Observers"]
                        UserObserver["UserObserver.php"]
                    end

                    subgraph Livewire ["Livewire"]
                        CountryUsers["CountryUsers.php"]
                        Notifications["Notifications.php"]
                    end

                    subgraph Console ["Console"]
                        Commands["Custom Commands"]
                    end
                end

                %% Relationships
                Enums --> Services
                Services --> Exports
                Services --> Imports
                Services --> Mail
                Mail --> Middlewares
                Observers --> Livewire

                UserRole --> RoleMiddleware
                MeetingStatus --> MeetingsExport
                InvestorStatus --> MeetingInvestorsExport
                OrganizationType --> OrganizationsExport

                classDef enumStyle fill:#fff3e0,stroke:#e65100,stroke-width:2px
                classDef exportStyle fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
                classDef serviceStyle fill:#e1f5fe,stroke:#01579b,stroke-width:2px
                classDef supportStyle fill:#f3e5f5,stroke:#4a148c,stroke-width:2px

                class Enums enumStyle
                class Exports,Imports exportStyle
                class Services serviceStyle
                class Support,Mail,Middlewares,Observers,Livewire,Console supportStyle
            ' />
    </section>
</x-app-layout>
