<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __("Dashboard") }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __("Diagrammes") }}</a></li>
                <li>{{ __("Processus d'Authentification") }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __("Processus d'Authentification") }}
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
                <h2 class="card-title text-primary">{{ __("Flux d'authentification utilisateur") }}</h2>
                <p class="text-base-content/70 mb-4">
                    Ce diagramme illustre le processus complet d'authentification d'un utilisateur dans le système BMCE Invest.
                    Il couvre les étapes de connexion, validation des identifiants, vérification des permissions et redirection selon le rôle.
                    Le diagramme montre également la gestion des erreurs et les différents chemins d'authentification possibles selon le statut de l'utilisateur.
                </p>
            </div>
        </div>

        <x-diagram.mermaid
            title="Authentification Utilisateur Complète"
            description="Ce diagramme de séquence détaille le processus technique d'authentification dans le système BMCE Invest. Il montre les interactions entre l'utilisateur, le contrôleur de connexion, les middlewares, les modèles de données et la session, incluant la validation des identifiants, la vérification des rôles et la gestion des redirections selon les permissions."
            definition="sequenceDiagram
                participant U as Utilisateur
                participant B as Navigateur
                participant LC as LoginController
                participant AM as AuthMiddleware
                participant UM as UserModel
                participant OM as OrganizationModel
                participant RM as RoleModel
                participant S as Session
                participant DB as Base de Données

                Note over U,DB: Processus d'Authentification BMCE Invest

                %% Phase 1: Accès à la page de connexion
                U->>B: Accède à /login
                B->>LC: GET /login
                LC->>B: Retourne formulaire de connexion
                B->>U: Affiche page de connexion

                %% Phase 2: Soumission des identifiants
                U->>B: Saisit email/password + soumet
                B->>LC: POST /login {email, password}

                %% Phase 3: Validation des données
                LC->>LC: Valide les données (required, email format)

                alt Données invalides
                    LC->>B: Retourne erreurs de validation
                    B->>U: Affiche erreurs de saisie
                else Données valides
                    %% Phase 4: Vérification en base de données
                    LC->>DB: SELECT user WHERE email = ?
                    DB->>UM: Retourne utilisateur ou null

                    alt Utilisateur non trouvé
                        UM->>LC: null
                        LC->>B: Erreur 'Identifiants incorrects'
                        B->>U: Affiche message d'erreur
                    else Utilisateur trouvé
                        UM->>LC: Objet User

                        %% Phase 5: Vérification du mot de passe
                        LC->>LC: Hash::check(password, user.password)

                        alt Mot de passe incorrect
                            LC->>B: Erreur 'Identifiants incorrects'
                            B->>U: Affiche message d'erreur
                        else Mot de passe correct
                            %% Phase 6: Vérification du statut utilisateur
                            alt Utilisateur inactif
                                LC->>B: Erreur 'Compte désactivé'
                                B->>U: Affiche message de compte désactivé
                            else Utilisateur actif
                                %% Phase 7: Chargement des relations
                                LC->>DB: Charger organization, roles, permissions
                                DB->>OM: Organization data
                                DB->>RM: Roles et permissions
                                OM->>LC: Données organisation
                                RM->>LC: Rôles et permissions

                                %% Phase 8: Création de la session
                                LC->>S: Auth::login(user, remember)
                                S->>S: Génère session ID
                                S->>S: Stocke données utilisateur
                                S->>LC: Session créée

                                %% Phase 9: Définition des données de session
                                LC->>S: session(['user_id' => user.id])
                                LC->>S: session(['organization_id' => org.id])
                                LC->>S: session(['user_roles' => roles])
                                LC->>S: session(['user_permissions' => permissions])

                                %% Phase 10: Redirection selon le rôle
                                alt Super Admin ou Admin
                                    LC->>B: redirect('/admin/dashboard')
                                    B->>AM: Vérification middleware
                                    AM->>S: Vérifie session et permissions
                                    S->>AM: Session valide + permissions admin
                                    AM->>B: Accès autorisé
                                    B->>U: Dashboard Administrateur
                                else Issuer (Émetteur)
                                    LC->>B: redirect('/issuer/dashboard')
                                    B->>AM: Vérification middleware
                                    AM->>S: Vérifie session et rôle issuer
                                    S->>AM: Session valide + rôle issuer
                                    AM->>B: Accès autorisé
                                    B->>U: Dashboard Émetteur
                                else Investor (Investisseur)
                                    LC->>B: redirect('/investor/dashboard')
                                    B->>AM: Vérification middleware
                                    AM->>S: Vérifie session et rôle investor
                                    S->>AM: Session valide + rôle investor
                                    AM->>B: Accès autorisé
                                    B->>U: Dashboard Investisseur
                                else Rôle non reconnu
                                    LC->>B: redirect('/dashboard')
                                    B->>U: Dashboard par défaut
                                end
                            end
                        end
                    end
                end

                Note over U,DB: Session établie - Utilisateur connecté

                %% Phase 11: Navigation ultérieure avec middleware
                U->>B: Accède à une page protégée
                B->>AM: Middleware auth vérifie l'accès
                AM->>S: Vérifie session active

                alt Session expirée
                    S->>AM: Session non trouvée/expirée
                    AM->>B: redirect('/login')
                    B->>U: Redirection vers connexion
                else Session valide
                    S->>AM: Session active + données utilisateur
                    AM->>AM: Vérifie permissions pour la route

                    alt Permissions insuffisantes
                        AM->>B: Erreur 403 Forbidden
                        B->>U: Page d'erreur d'accès
                    else Permissions suffisantes
                        AM->>B: Accès autorisé
                        B->>U: Page demandée
                    end
                end
            "
        />
    </section>
</x-app-layout>
