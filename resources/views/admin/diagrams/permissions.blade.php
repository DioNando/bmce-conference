<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __('Diagrammes') }}</a></li>
                <li>{{ __('Système de Permissions') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Architecture des Permissions') }}
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
                <h2 class="card-title text-primary">{{ __('Système de Rôles et Permissions') }}</h2>
                <p class="text-base-content/70 mb-4">
                    Ce diagramme détaille l'architecture du système de permissions basé sur le package Spatie Laravel
                    Permission.
                    Il montre comment les rôles et permissions sont structurés pour gérer l'accès aux différentes
                    fonctionnalités du système.
                    Cette architecture flexible permet une gestion granulaire des droits d'accès selon les profils
                    utilisateurs (Admin, Investor, Issuer).
                </p>
            </div>
        </div>

        <x-diagram.mermaid title="Architecture Spatie Laravel Permission"
            description="Ce diagramme de classes présente l'architecture complète du système de permissions utilisant le package Spatie Laravel Permission. Il montre les relations entre les modèles User, Role et Permission, ainsi que les tables pivot et les méthodes disponibles pour la gestion des droits d'accès."
            definition="classDiagram
                %% Core Permission Classes (Spatie Package)
                class Role {
                    +id: bigint
                    +name: string
                    +guard_name: string
                    +created_at: timestamp
                    +updated_at: timestamp
                    +permissions() Collection~Permission~
                    +users() Collection~User~
                    +givePermissionTo(permission: Permission|string) Role
                    +revokePermissionTo(permission: Permission|string) Role
                    +hasPermissionTo(permission: Permission|string) bool
                    +syncPermissions(permissions: Collection) Role
                }

                class Permission {
                    +id: bigint
                    +name: string
                    +guard_name: string
                    +created_at: timestamp
                    +updated_at: timestamp
                    +roles() Collection~Role~
                    +users() Collection~User~
                    +assignRole(role: Role|string) Permission
                    +removeRole(role: Role|string) Permission
                }

                %% Pivot Tables (Spatie Package)
                class ModelHasPermissions {
                    +permission_id: bigint
                    +model_type: string
                    +model_id: bigint
                    +permission() Permission
                }

                class ModelHasRoles {
                    +role_id: bigint
                    +model_type: string
                    +model_id: bigint
                    +role() Role
                }

                class RoleHasPermissions {
                    +permission_id: bigint
                    +role_id: bigint
                    +permission() Permission
                    +role() Role
                }

                %% Application User Model (Extended)
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
                    +roles() Collection~Role~
                    +permissions() Collection~Permission~
                    +assignRole(role: Role|string) User
                    +removeRole(role: Role|string) User
                    +givePermissionTo(permission: Permission|string) User
                    +revokePermissionTo(permission: Permission|string) User
                    +hasRole(role: Role|string|Collection) bool
                    +hasAnyRole(roles: Collection|array) bool
                    +hasAllRoles(roles: Collection|array) bool
                    +hasPermissionTo(permission: Permission|string) bool
                    +hasAnyPermission(permissions: Collection|array) bool
                    +hasAllPermissions(permissions: Collection|array) bool
                    +hasDirectPermission(permission: Permission|string) bool
                    +getPermissionsViaRoles() Collection~Permission~
                    +getAllPermissions() Collection~Permission~
                    +getDirectPermissions() Collection~Permission~
                    +getRoleNames() Collection~string~
                    +getPermissionNames() Collection~string~
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
                    +isIssuer() bool
                    +isInvestor() bool
                }

                %% Application-Specific Permission Groups
                class PermissionGroup {
                    <<enumeration>>
                    USER_MANAGEMENT
                    ORGANIZATION_MANAGEMENT
                    MEETING_MANAGEMENT
                    ROOM_MANAGEMENT
                    REPORTING
                    SYSTEM_ADMINISTRATION
                }

                class SystemRoles {
                    <<enumeration>>
                    SUPER_ADMIN
                    ADMIN
                    ISSUER_ADMIN
                    ISSUER_USER
                    INVESTOR_ADMIN
                    INVESTOR_USER
                    VIEWER
                }

                class SystemPermissions {
                    <<enumeration>>
                    %% User Management
                    VIEW_USERS
                    CREATE_USERS
                    EDIT_USERS
                    DELETE_USERS
                    MANAGE_USER_ROLES
                    %% Organization Management
                    VIEW_ORGANIZATIONS
                    CREATE_ORGANIZATIONS
                    EDIT_ORGANIZATIONS
                    DELETE_ORGANIZATIONS
                    MANAGE_ORGANIZATION_USERS
                    %% Meeting Management
                    VIEW_MEETINGS
                    CREATE_MEETINGS
                    EDIT_MEETINGS
                    DELETE_MEETINGS
                    PUBLISH_MEETINGS
                    MANAGE_MEETING_PARTICIPANTS
                    BOOK_MEETING_SLOTS
                    MANAGE_MEETING_SLOTS
                    VIEW_MEETING_QUESTIONS
                    ANSWER_MEETING_QUESTIONS
                    %% Room Management
                    VIEW_ROOMS
                    CREATE_ROOMS
                    EDIT_ROOMS
                    DELETE_ROOMS
                    MANAGE_ROOM_AVAILABILITY
                    %% Reporting
                    VIEW_REPORTS
                    EXPORT_DATA
                    VIEW_ANALYTICS
                    %% System Administration
                    MANAGE_SYSTEM_SETTINGS
                    VIEW_SYSTEM_LOGS
                    MANAGE_PERMISSIONS
                    MANAGE_ROLES
                    BACKUP_SYSTEM
                }

                %% Relationships
                User ||--o{ ModelHasRoles : polymorphic
                User ||--o{ ModelHasPermissions : polymorphic
                Role ||--o{ ModelHasRoles : has_many
                Role ||--o{ RoleHasPermissions : has_many
                Permission ||--o{ ModelHasPermissions : has_many
                Permission ||--o{ RoleHasPermissions : has_many
                User ||--o{ Organization : belongs_to

                %% Many-to-Many relationships through pivot tables
                Role }|--|| RoleHasPermissions : through
                Permission }|--|| RoleHasPermissions : through
                User }|--|| ModelHasRoles : through
                Role }|--|| ModelHasRoles : through
                User }|--|| ModelHasPermissions : through
                Permission }|--|| ModelHasPermissions : through
            " />
    </section>
</x-app-layout>
