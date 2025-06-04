# Diagramme de Classes - Système de Permissions et Rôles

## Description
Ce diagramme se concentre spécifiquement sur l'architecture du système de permissions et de rôles utilisant le package Spatie Laravel Permission. Il détaille les relations entre utilisateurs, rôles et permissions, ainsi que les tables pivot nécessaires. Cette structure permet une gestion granulaire des accès et une attribution flexible des droits selon les besoins métier.

```mermaid
classDiagram
    %% Core Permission System
    class User {
        +id: bigint
        +name: string
        +email: string
        +organization_id: bigint
        +status: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +assignRole(role: string|Role) User
        +removeRole(role: string|Role) User
        +syncRoles(roles: Array) User
        +hasRole(role: string|Role) bool
        +hasAnyRole(roles: Array) bool
        +hasAllRoles(roles: Array) bool
        +givePermissionTo(permission: string|Permission) User
        +revokePermissionTo(permission: string|Permission) User
        +syncPermissions(permissions: Array) User
        +can(permission: string) bool
        +cannot(permission: string) bool
        +hasPermissionTo(permission: string|Permission) bool
        +hasAnyPermission(permissions: Array) bool
        +hasAllPermissions(permissions: Array) bool
        +getPermissionsViaRoles() Collection~Permission~
        +getDirectPermissions() Collection~Permission~
        +getAllPermissions() Collection~Permission~
        +roles() BelongsToMany~Role~
        +permissions() BelongsToMany~Permission~
    }

    class Role {
        +id: bigint
        +name: string
        +guard_name: string
        +description: string
        +level: integer
        +is_system: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +givePermissionTo(permission: string|Permission) Role
        +revokePermissionTo(permission: string|Permission) Role
        +syncPermissions(permissions: Array) Role
        +hasPermissionTo(permission: string|Permission) bool
        +hasAnyPermission(permissions: Array) bool
        +hasAllPermissions(permissions: Array) bool
        +permissions() BelongsToMany~Permission~
        +users() BelongsToMany~User~
        +getPermissionNames() Collection~string~
        +getUsersCount() int
    }

    class Permission {
        +id: bigint
        +name: string
        +guard_name: string
        +description: string
        +group: string
        +module: string
        +action: string
        +resource: string
        +is_system: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +roles() BelongsToMany~Role~
        +users() BelongsToMany~User~
        +assignedUsers() Collection~User~
        +assignedRoles() Collection~Role~
        +getFullName() string
        +belongsToGroup(group: string) bool
    }

    %% Pivot Tables
    class ModelHasRoles {
        +role_id: bigint
        +model_type: string
        +model_id: bigint
        +assigned_at: timestamp
        +assigned_by: bigint
        +expires_at: timestamp
        +role() BelongsTo~Role~
        +model() MorphTo
        +assignedBy() BelongsTo~User~
        +isExpired() bool
        +isActive() bool
    }

    class ModelHasPermissions {
        +permission_id: bigint
        +model_type: string
        +model_id: bigint
        +assigned_at: timestamp
        +assigned_by: bigint
        +expires_at: timestamp
        +permission() BelongsTo~Permission~
        +model() MorphTo
        +assignedBy() BelongsTo~User~
        +isExpired() bool
        +isActive() bool
    }

    class RoleHasPermissions {
        +permission_id: bigint
        +role_id: bigint
        +assigned_at: timestamp
        +assigned_by: bigint
        +role() BelongsTo~Role~
        +permission() BelongsTo~Permission~
        +assignedBy() BelongsTo~User~
    }

    %% Supporting Classes for Business Logic
    class PermissionGroup {
        +id: bigint
        +name: string
        +slug: string
        +description: text
        +icon: string
        +sort_order: integer
        +is_active: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +permissions() HasMany~Permission~
        +getPermissionsCount() int
    }

    class UserRoleHistory {
        +id: bigint
        +user_id: bigint
        +role_id: bigint
        +action: enum[assigned, revoked]
        +performed_by: bigint
        +reason: text
        +metadata: json
        +created_at: timestamp
        +user() BelongsTo~User~
        +role() BelongsTo~Role~
        +performedBy() BelongsTo~User~
    }

    %% Helper Classes for Authorization
    class PermissionChecker {
        +checkUserPermission(user: User, permission: string) bool
        +checkRolePermission(role: Role, permission: string) bool
        +getUserPermissions(user: User) Collection~Permission~
        +getUserRoles(user: User) Collection~Role~
        +canAccessResource(user: User, resource: string, action: string) bool
        +hasOrganizationAccess(user: User, organization: Organization) bool
        +hasModuleAccess(user: User, module: string) bool
    }

    class RoleManager {
        +createRole(name: string, permissions: Array) Role
        +updateRole(role: Role, permissions: Array) Role
        +deleteRole(role: Role) bool
        +assignRoleToUser(user: User, role: Role) bool
        +removeRoleFromUser(user: User, role: Role) bool
        +syncUserRoles(user: User, roles: Array) bool
        +getDefaultRoleForProfile(profile: string) Role
        +validateRoleAssignment(user: User, role: Role) bool
    }

    class PermissionManager {
        +createPermission(name: string, group: string) Permission
        +updatePermission(permission: Permission, data: Array) Permission
        +deletePermission(permission: Permission) bool
        +grantPermissionToRole(role: Role, permission: Permission) bool
        +revokePermissionFromRole(role: Role, permission: Permission) bool
        +grantPermissionToUser(user: User, permission: Permission) bool
        +revokePermissionFromUser(user: User, permission: Permission) bool
        +syncRolePermissions(role: Role, permissions: Array) bool
    }

    %% Organization Context for Permissions
    class Organization {
        +id: bigint
        +name: string
        +profil: enum[issuer, investor]
        +created_at: timestamp
        +updated_at: timestamp
        +users() HasMany~User~
        +getAdminUsers() Collection~User~
        +getActiveUsers() Collection~User~
        +hasPermissionInContext(user: User, permission: string) bool
    }

    %% Relationships
    User }o--o{ Role : ModelHasRoles
    User }o--o{ Permission : ModelHasPermissions
    Role }o--o{ Permission : RoleHasPermissions
    User ||--|| Organization : belongs_to

    %% Direct Relationships
    User ||--o{ ModelHasRoles : has_roles
    User ||--o{ ModelHasPermissions : has_permissions
    User ||--o{ UserRoleHistory : role_history
    
    Role ||--o{ ModelHasRoles : assigned_to_users
    Role ||--o{ RoleHasPermissions : has_permissions
    Role ||--o{ UserRoleHistory : history
    
    Permission ||--o{ ModelHasPermissions : assigned_to_users
    Permission ||--o{ RoleHasPermissions : assigned_to_roles
    Permission ||--|| PermissionGroup : belongs_to
    
    PermissionGroup ||--o{ Permission : contains

    %% Helper Relationships
    PermissionChecker ..> User : checks
    PermissionChecker ..> Role : checks
    PermissionChecker ..> Permission : checks
    
    RoleManager ..> Role : manages
    RoleManager ..> User : assigns_roles
    
    PermissionManager ..> Permission : manages
    PermissionManager ..> Role : grants_permissions
    PermissionManager ..> User : grants_permissions

    %% Business Rules and Constraints
    note for User "Un utilisateur peut avoir plusieurs rôles\net permissions directes"
    
    note for Role "Les rôles sont hiérarchiques\n(admin > manager > user)"
    
    note for Permission "Les permissions sont groupées\npar module fonctionnel"
    
    note for ModelHasRoles "Stocke l'historique des\nassignations de rôles"
    
    note for Organization "Le contexte organisationnel\ninfluence les permissions"

    %% System Permissions Examples
    class SystemPermissions {
        <<enumeration>>
        ADMIN_ACCESS
        USER_MANAGEMENT_CREATE
        USER_MANAGEMENT_READ
        USER_MANAGEMENT_UPDATE
        USER_MANAGEMENT_DELETE
        ORGANIZATION_MANAGEMENT_CREATE
        ORGANIZATION_MANAGEMENT_READ
        ORGANIZATION_MANAGEMENT_UPDATE
        ORGANIZATION_MANAGEMENT_DELETE
        MEETING_CREATE
        MEETING_READ
        MEETING_UPDATE
        MEETING_DELETE
        MEETING_MANAGE_ALL
        REPORTS_VIEW
        REPORTS_EXPORT
        SYSTEM_SETTINGS_ACCESS
        NOTIFICATIONS_SEND
        ROLES_MANAGE
        PERMISSIONS_MANAGE
    }

    %% Default Roles
    class DefaultRoles {
        <<enumeration>>
        SUPER_ADMIN
        ADMIN
        ISSUER_ADMIN
        ISSUER_USER
        INVESTOR_ADMIN
        INVESTOR_USER
        GUEST
    }

    SystemPermissions ..> Permission : defines
    DefaultRoles ..> Role : defines

    %% Styling
    classDef coreClass fill:#e3f2fd
    classDef pivotClass fill:#fff3e0
    classDef helperClass fill:#e8f5e8
    classDef enumClass fill:#fce4ec
    
    class User,Role,Permission,Organization coreClass
    class ModelHasRoles,ModelHasPermissions,RoleHasPermissions,UserRoleHistory pivotClass
    class PermissionChecker,RoleManager,PermissionManager,PermissionGroup helperClass
    class SystemPermissions,DefaultRoles enumClass
```
