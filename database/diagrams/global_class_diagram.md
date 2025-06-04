# Diagramme de Classes Globale - Système BMCE Invest

## Description
Ce diagramme présente une vue d'ensemble complète de toutes les entités du système BMCE Invest et leurs relations. Il montre l'architecture globale du domaine métier, incluant la gestion des utilisateurs, organisations, rendez-vous, salles et permissions. Cette vue permet de comprendre rapidement la structure complète du système et les interactions entre tous les composants.

```mermaid
classDiagram
    %% Core User Management
    class User {
        +id: bigint
        +name: string
        +first_name: string
        +email: string
        +password: string
        +phone: string
        +position: string
        +organization_id: bigint
        +status: boolean
        +email_verified_at: timestamp
        +remember_token: string
        +created_at: timestamp
        +updated_at: timestamp
        +organization() Organization
        +issuerMeetings() Collection~Meeting~
        +investorMeetings() Collection~Meeting~
        +questions() Collection~Question~
        +createdMeetings() Collection~Meeting~
        +updatedMeetings() Collection~Meeting~
        +timeSlots() Collection~TimeSlot~
        +roles() Collection~Role~
        +permissions() Collection~Permission~
        +hasRole(role: string) bool
        +can(permission: string) bool
    }

    class Organization {
        +id: bigint
        +name: string
        +origin: enum[national, foreign]
        +profil: enum[issuer, investor]
        +organization_type: string
        +organization_type_other: string
        +logo: string
        +country_id: bigint
        +description: text
        +address: string
        +website: string
        +created_at: timestamp
        +updated_at: timestamp
        +users() Collection~User~
        +country() Country
        +getUsersCount() int
        +getActiveUsersCount() int
    }

    class Country {
        +id: bigint
        +name_fr: string
        +name_en: string
        +code: string
        +flag: string
        +created_at: timestamp
        +updated_at: timestamp
        +organizations() Collection~Organization~
        +getOrganizationsCount() int
    }

    %% Meeting Management
    class Meeting {
        +id: bigint
        +room_id: bigint
        +time_slot_id: bigint
        +issuer_id: bigint
        +created_by_id: bigint
        +updated_by_id: bigint
        +status: enum[pending, confirmed, cancelled]
        +notes: text
        +is_one_on_one: boolean
        +meeting_link: string
        +created_at: timestamp
        +updated_at: timestamp
        +room() Room
        +timeSlot() TimeSlot
        +issuer() User
        +investors() Collection~User~
        +meetingInvestors() Collection~MeetingInvestor~
        +createdBy() User
        +updatedBy() User
        +questions() Collection~Question~
        +isConfirmed() bool
        +isPending() bool
        +isCancelled() bool
    }

    class MeetingInvestor {
        +id: bigint
        +meeting_id: bigint
        +investor_id: bigint
        +status: enum[pending, confirmed, declined]
        +joined_at: timestamp
        +notes: text
        +created_at: timestamp
        +updated_at: timestamp
        +meeting() Meeting
        +investor() User
        +isConfirmed() bool
        +isPending() bool
        +isDeclined() bool
    }

    class TimeSlot {
        +id: bigint
        +user_id: bigint
        +date: date
        +start_time: time
        +end_time: time
        +availability: boolean
        +is_recurring: boolean
        +recurring_pattern: string
        +created_at: timestamp
        +updated_at: timestamp
        +meetings() Collection~Meeting~
        +user() User
        +isAvailable() bool
        +getDuration() int
    }

    class Question {
        +id: bigint
        +meeting_id: bigint
        +investor_id: bigint
        +question: text
        +answer: text
        +is_answered: boolean
        +answered_at: timestamp
        +answered_by_id: bigint
        +priority: enum[low, medium, high]
        +created_at: timestamp
        +updated_at: timestamp
        +meeting() Meeting
        +askedBy() User
        +answeredBy() User
        +markAsAnswered() void
    }

    class Room {
        +id: bigint
        +name: string
        +capacity: integer
        +location: string
        +description: text
        +equipment: text
        +is_active: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +meetings() Collection~Meeting~
        +isAvailable(date: date, startTime: time, endTime: time) bool
        +getAvailableTimes(date: date) Collection
    }

    %% Permission System
    class Role {
        +id: bigint
        +name: string
        +guard_name: string
        +description: string
        +created_at: timestamp
        +updated_at: timestamp
        +permissions() Collection~Permission~
        +users() Collection~User~
        +givePermissionTo(permission: Permission) void
        +revokePermissionTo(permission: Permission) void
    }

    class Permission {
        +id: bigint
        +name: string
        +guard_name: string
        +description: string
        +group: string
        +created_at: timestamp
        +updated_at: timestamp
        +roles() Collection~Role~
        +users() Collection~User~
    }

    %% Pivot Tables
    class ModelHasRoles {
        +role_id: bigint
        +model_type: string
        +model_id: bigint
    }

    class ModelHasPermissions {
        +permission_id: bigint
        +model_type: string
        +model_id: bigint
    }

    class RoleHasPermissions {
        +permission_id: bigint
        +role_id: bigint
    }

    %% Core Relationships
    User ||--|| Organization : belongs_to
    Organization ||--|| Country : belongs_to
    User ||--o{ TimeSlot : has_many
    User ||--o{ Meeting : creates_as_issuer
    User ||--o{ Meeting : created_by
    User ||--o{ Meeting : updated_by

    %% Meeting Relationships
    Meeting ||--|| Room : belongs_to
    Meeting ||--|| TimeSlot : belongs_to
    Meeting ||--|| User : issuer
    Meeting ||--o{ MeetingInvestor : has_many
    Meeting ||--o{ Question : has_many

    %% Investor Relationships
    MeetingInvestor ||--|| Meeting : belongs_to
    MeetingInvestor ||--|| User : investor

    %% Question Relationships
    Question ||--|| Meeting : belongs_to
    Question ||--|| User : asked_by
    Question ||--o| User : answered_by

    %% Permission Relationships
    User ||--o{ ModelHasRoles : has_roles
    User ||--o{ ModelHasPermissions : has_permissions
    Role ||--o{ RoleHasPermissions : has_permissions
    Role ||--o{ ModelHasRoles : assigned_to_users
    Permission ||--o{ ModelHasPermissions : assigned_to_users
    Permission ||--o{ RoleHasPermissions : assigned_to_roles

    %% Many-to-Many through Pivot
    User }o--o{ Role : through_ModelHasRoles
    User }o--o{ Permission : through_ModelHasPermissions
    Role }o--o{ Permission : through_RoleHasPermissions

    %% Styling
    classDef userClass fill:#e1f5fe
    classDef meetingClass fill:#f3e5f5
    classDef systemClass fill:#e8f5e8
    classDef permissionClass fill:#fff3e0

    class User,Organization,Country userClass
    class Meeting,MeetingInvestor,TimeSlot,Question,Room meetingClass
    class Role,Permission,ModelHasRoles,ModelHasPermissions,RoleHasPermissions permissionClass
```
