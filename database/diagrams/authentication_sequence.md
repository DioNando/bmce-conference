# Diagramme de Séquence - Authentification Utilisateur

## Description
Ce diagramme illustre le processus complet d'authentification d'un utilisateur dans le système BMCE Invest. Il couvre les étapes de connexion, validation des identifiants, vérification des permissions et redirection selon le rôle. Le diagramme montre également la gestion des erreurs et les différents chemins d'authentification possibles selon le statut de l'utilisateur.

```mermaid
sequenceDiagram
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
        LC->>B: Erreurs de validation
        B->>U: Affiche erreurs
    else Données valides
        
        %% Phase 4: Vérification des identifiants
        LC->>UM: attempt({email, password})
        UM->>DB: SELECT * FROM users WHERE email = ?
        DB->>UM: Retourne données utilisateur ou null
        
        alt Utilisateur non trouvé ou mot de passe incorrect
            UM->>LC: Échec authentification
            LC->>B: Erreur "Identifiants invalides"
            B->>U: Affiche erreur
        else Identifiants corrects
            
            %% Phase 5: Vérification du statut utilisateur
            UM->>UM: Vérifie status = true
            alt Utilisateur inactif
                UM->>LC: Utilisateur inactif
                LC->>B: Erreur "Compte désactivé"
                B->>U: Affiche erreur
            else Utilisateur actif
                
                %% Phase 6: Vérification email vérifié
                UM->>UM: Vérifie email_verified_at
                alt Email non vérifié
                    UM->>LC: Email non vérifié
                    LC->>B: Redirection vers vérification email
                    B->>U: Page de vérification email
                else Email vérifié
                    
                    %% Phase 7: Chargement des relations
                    UM->>DB: Load organization avec country
                    DB->>UM: Données organisation et pays
                    UM->>DB: Load roles avec permissions
                    DB->>RM: Données rôles et permissions
                    RM->>UM: Rôles et permissions de l'utilisateur
                    
                    %% Phase 8: Création de la session
                    LC->>S: Créer session utilisateur
                    S->>S: Stocke user_id, remember_token
                    LC->>S: Stocke données utilisateur en session
                    S->>S: user_data = {id, name, email, organization, roles}
                    
                    %% Phase 9: Régénération de session (sécurité)
                    LC->>S: regenerate() - nouvelle session ID
                    
                    %% Phase 10: Détermination de la redirection
                    LC->>RM: Vérifie rôle principal de l'utilisateur
                    alt Utilisateur admin
                        LC->>B: Redirection vers /admin/dashboard
                    else Utilisateur issuer
                        LC->>B: Redirection vers /issuer/dashboard
                    else Utilisateur investor
                        LC->>B: Redirection vers /investor/dashboard
                    else Autre rôle
                        LC->>B: Redirection vers /dashboard (défaut)
                    end
                    
                    %% Phase 11: Middleware et accès au dashboard
                    B->>AM: Accès au dashboard
                    AM->>S: Vérifie session active
                    S->>AM: Session valide
                    AM->>UM: Charge utilisateur depuis session
                    UM->>DB: SELECT user avec relations
                    DB->>UM: Données utilisateur complètes
                    UM->>AM: Utilisateur authentifié
                    AM->>B: Accès autorisé au dashboard
                    B->>U: Affiche dashboard approprié
                end
            end
        end
    end

    Note over U,DB: Processus de Déconnexion

    %% Phase 12: Déconnexion
    U->>B: Clique sur déconnexion
    B->>LC: POST /logout
    LC->>S: Détruire session
    S->>S: flush() + invalidate()
    LC->>S: regenerateToken()
    LC->>B: Redirection vers /
    B->>U: Page d'accueil (déconnecté)

    Note over U,DB: Gestion des Erreurs et Timeouts

    %% Timeout de session
    rect rgb(255, 240, 240)
        Note over S: Session expire après inactivité
        U->>B: Tente d'accéder à une page protégée
        B->>AM: Vérification authentification
        AM->>S: Vérifie session
        S->>AM: Session expirée
        AM->>B: Redirection vers /login
        B->>U: Formulaire de connexion + message "Session expirée"
    end

    %% Tentatives multiples échouées
    rect rgb(255, 240, 240)
        Note over LC: Protection contre brute force
        U->>B: Tentative de connexion échouée
        B->>LC: POST /login (échec)
        LC->>LC: Incrémente compteur tentatives
        alt Trop de tentatives
            LC->>B: Compte temporairement bloqué
            B->>U: Message "Trop de tentatives, réessayez plus tard"
        end
    end
```
