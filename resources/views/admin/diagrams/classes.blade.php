<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __('Diagrammes') }}</a></li>
                <li>{{ __('Diagrammes de Classes') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Diagrammes de Classes') }}
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
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-primary">{{ __("Vue d'ensemble des modèles") }}</h2>
                <p class="text-base-content/70 mb-4">
                    Ces diagrammes présentent les entités métier principales du système BMCE Invest et leurs relations.
                    Ils couvrent la gestion des utilisateurs, organisations, meetings et le système de permissions.
                    Ces modèles forment la base de l'architecture du domaine métier.
                </p>
            </div>
        </div>

        <x-diagram.mermaid title="Architecture Globale du Système"
            description="Ce diagramme présente l'architecture complète des modèles du système BMCE Invest avec toutes les entités métier et leurs relations. Il illustre la structure des utilisateurs, organisations, meetings, créneaux horaires et questions, formant le cœur du domaine métier. Cette vue d'ensemble permet de comprendre les interactions complexes entre les différentes composantes du système de gestion des meetings d'investissement."
            definition="classDiagram
                %% Core User Management
                class User {
                    +int id
                    +string firstName
                    +string name
                    +string email
                    +string password
                    +string phone
                    +string position
                    +string qrCode
                    +int organizationId
                    +boolean status
                    +datetime emailVerifiedAt
                    +string rememberToken
                    +datetime createdAt
                    +datetime updatedAt
                    +organization() Organization
                    +timeSlots() TimeSlot
                    +questions() Question
                    +issuerMeetings() Meeting
                    +investorMeetings() Meeting
                    +createdMeetings() Meeting
                    +updatedMeetings() Meeting
                    +isInvestor() boolean
                    +isIssuer() boolean
                    +isAdmin() boolean
                }

                class Organization {
                    +int id
                    +string name
                    +string origin
                    +string profil
                    +string organizationType
                    +string organizationTypeOther
                    +string logo
                    +string ficheBkgr
                    +int countryId
                    +string description
                    +datetime createdAt
                    +datetime updatedAt
                    +users() User
                    +country() Country
                }

                class Country {
                    +int id
                    +string name
                    +string iso
                    +datetime createdAt
                    +datetime updatedAt
                    +organizations() Organization
                }

                %% Meeting System
                class Meeting {
                    +int id
                    +int roomId
                    +int timeSlotId
                    +int issuerId
                    +int createdById
                    +int updatedById
                    +string status
                    +string notes
                    +boolean isOneOnOne
                    +datetime createdAt
                    +datetime updatedAt
                    +room() Room
                    +timeSlot() TimeSlot
                    +issuer() User
                    +investors() User
                    +meetingInvestors() MeetingInvestor
                    +questions() Question
                    +createdBy() User
                    +updatedBy() User
                }

                class Room {
                    +int id
                    +string name
                    +string location
                    +int capacity
                    +datetime createdAt
                    +datetime updatedAt
                    +meetings() Meeting
                }

                class TimeSlot {
                    +int id
                    +date date
                    +time startTime
                    +time endTime
                    +boolean availability
                    +int userId
                    +int eventId
                    +datetime createdAt
                    +datetime updatedAt
                    +event() Event
                    +meetings() Meeting
                    +user() User
                }

                class Event {
                    +int id
                    +string name
                    +string description
                    +date startDate
                    +date endDate
                    +string location
                    +boolean isActive
                    +datetime createdAt
                    +datetime updatedAt
                    +timeSlots() TimeSlot
                }

                class Question {
                    +int id
                    +int meetingId
                    +int investorId
                    +string question
                    +string response
                    +datetime answeredAt
                    +boolean isAnswered
                    +datetime createdAt
                    +datetime updatedAt
                    +meeting() Meeting
                    +investor() User
                }

                %% Pivot Table
                class MeetingInvestor {
                    +int id
                    +int meetingId
                    +int investorId
                    +string status
                    +boolean invitationSent
                    +datetime invitationSentAt
                    +datetime checkedInAt
                    +int checkedInBy
                    +datetime createdAt
                    +datetime updatedAt
                    +meeting() Meeting
                    +investor() User
                    +checkedInBy() User
                }

                %% Relationships
                User *-- Organization : belongs_to
                Organization *-- Country : belongs_to
                User o-- TimeSlot : has_many
                User o-- Meeting : issuer_meetings
                User o-- Meeting : created_meetings
                User o-- Meeting : updated_meetings
                User o-- Question : questions
                Meeting *-- User : issuer
                Meeting *-- User : created_by
                Meeting *-- User : updated_by
                Meeting *-- Room : belongs_to
                Meeting *-- TimeSlot : belongs_to
                Meeting o-- Question : has_many
                Meeting o-- MeetingInvestor : has_many
                MeetingInvestor *-- Meeting : belongs_to
                MeetingInvestor *-- User : investor
                MeetingInvestor --o User : checked_in_by
                Question *-- Meeting : belongs_to
                Question *-- User : investor
                TimeSlot *-- User : belongs_to
                TimeSlot --o Event : belongs_to
                Event o-- TimeSlot : has_many" />

        <x-diagram.mermaid title="Modèle utilisateur et authentification"
            description="Ce diagramme détaille la structure des utilisateurs et le système d'authentification avec les rôles et permissions associés. Il montre les relations entre utilisateurs, organisations et les différents types de profils (ADMIN, INVESTOR, ISSUER). Cette architecture permet une gestion fine des accès et des responsabilités dans l'écosystème BMCE Invest."
            definition="classDiagram
                class User {
                    +string name
                    +string email
                    +string password
                    +Organization organization
                    +UserRole role
                    +string permissions
                    +isAdmin()
                    +isInvestor()
                    +isIssuer()
                }
                class Organization {
                    +string name
                    +string description
                    +OrganizationType type
                    +Country country
                    +User users
                }
                class UserRole {
                    <<enumeration>>
                    ADMIN
                    INVESTOR
                    ISSUER
                }
                class OrganizationType {
                    <<enumeration>>
                    INVESTOR
                    ISSUER
                }
                User *-- UserRole
                User o-- Organization
                Organization *-- OrganizationType" />

        <x-diagram.mermaid title="Gestion des meetings"
            description="Ce diagramme focus sur l'écosystème complet de gestion des meetings avec les entités Meeting, MeetingInvestor, TimeSlot, Room et Question. Il détaille les statuts des meetings et des participants, les créneaux horaires et le système de questions-réponses. Cette architecture supporte la planification, l'organisation et le suivi des rencontres entre émetteurs et investisseurs."
            definition="classDiagram
                class Meeting {
                    +int id
                    +int room_id
                    +int time_slot_id
                    +int issuer_id
                    +MeetingStatus status
                    +text notes
                    +boolean is_one_on_one
                    +timestamps created_at
                    +timestamps updated_at
                    +getIssuer() User
                    +getRoom() Room
                    +getTimeSlot() TimeSlot
                    +getInvestors() User[]
                    +getMeetingInvestors() MeetingInvestor[]
                    +getQuestions() Question[]
                }
                class MeetingInvestor {
                    +int id
                    +int meeting_id
                    +int investor_id
                    +InvestorStatus status
                    +boolean invitation_sent
                    +timestamp checked_in_at
                    +timestamps created_at
                    +timestamps updated_at
                    +getMeeting() Meeting
                    +getInvestor() User
                }
                class TimeSlot {
                    +int id
                    +int event_id
                    +int user_id
                    +datetime start_time
                    +datetime end_time
                    +timestamps created_at
                    +timestamps updated_at
                    +getEvent() Event
                    +getUser() User
                    +getMeetings() Meeting[]
                }
                class Room {
                    +int id
                    +string name
                    +string location
                    +int capacity
                    +timestamps created_at
                    +timestamps updated_at
                    +getMeetings() Meeting[]
                }
                class Question {
                    +int id
                    +int meeting_id
                    +int investor_id
                    +text question
                    +text answer
                    +timestamps created_at
                    +timestamps updated_at
                    +getMeeting() Meeting
                    +getInvestor() User
                }
                class User {
                    +int id
                    +string name
                    +string email
                    +int organization_id
                    +getOrganization() Organization
                    +getIssuerMeetings() Meeting[]
                    +getInvestorMeetings() Meeting[]
                    +getTimeSlots() TimeSlot[]
                    +getQuestions() Question[]
                }
                class MeetingStatus {
                    <<enumeration>>
                    PENDING
                    CONFIRMED
                    CANCELLED
                    SCHEDULED
                    COMPLETED
                    DECLINED
                }
                class InvestorStatus {
                    <<enumeration>>
                    PENDING
                    CONFIRMED
                    REFUSED
                    ATTENDED
                    ABSENT
                }

                Meeting *-- MeetingStatus : status
                Meeting o-- Room : room_id
                Meeting o-- TimeSlot : time_slot_id
                Meeting o-- User : issuer_id
                Meeting --* MeetingInvestor : meeting_id
                Meeting --* Question : meeting_id

                MeetingInvestor *-- InvestorStatus : status
                MeetingInvestor o-- Meeting : meeting_id
                MeetingInvestor o-- User : investor_id

                Question o-- Meeting : meeting_id
                Question o-- User : investor_id

                TimeSlot o-- User : user_id
                TimeSlot --* Meeting : time_slot_id

                Room --* Meeting : room_id

                User --* Meeting : issuer_id
                User --* MeetingInvestor : investor_id
                User --* Question : investor_id
                User --* TimeSlot : user_id
                " />
    </section>
</x-app-layout>
