# Index des Diagrammes - BMCE Invest

## Vue d'ensemble

Ce dossier contient tous les diagrammes techniques du système BMCE Invest, organisés par catégorie pour faciliter la compréhension de l'architecture et des processus métier.

## Diagrammes de Classes

### 1. Diagramme de Classes Principal (`class_diagram.md`)
**Description :** Présente les principales entités métier du système et leurs relations directes. Couvre la gestion des utilisateurs, organisations, rendez-vous et le système de permissions de base.
**Usage :** Point d'entrée pour comprendre la structure générale du domaine métier.

### 2. Diagramme de Classes Globale (`global_class_diagram.md`)
**Description :** Vue d'ensemble complète de toutes les entités du système avec leurs relations, méthodes et propriétés détaillées. Inclut également les tables pivot et les classes utilitaires.
**Usage :** Référence complète pour les développeurs nécessitant une vue exhaustive du système.

### 3. Système de Permissions (`permissions_class_diagram.md`)
**Description :** Architecture détaillée du système de rôles et permissions utilisant Spatie Laravel Permission. Montre les relations complexes entre utilisateurs, rôles et permissions avec les tables pivot.
**Usage :** Compréhension approfondie du système d'autorisation et de sécurité.

## Diagrammes de Séquence

### 4. Authentification (`authentication_sequence.md`)
**Description :** Processus complet d'authentification d'un utilisateur, incluant la validation des identifiants, vérification des permissions et gestion des erreurs. Couvre également la déconnexion et les timeouts de session.
**Usage :** Comprendre le flux de connexion et les mécanismes de sécurité.

### 5. Création de Rendez-vous (`meeting_creation_sequence.md`)
**Description :** Flux détaillé de création d'un rendez-vous entre émetteurs et investisseurs. Inclut la validation des disponibilités, gestion des conflits et notifications aux participants.
**Usage :** Comprendre le processus métier central de planification des rendez-vous.

### 6. Export de Données (`data_export_sequence.md`)
**Description :** Processus d'export de données vers Excel avec gestion des gros volumes, exports en arrière-plan et personnalisation des colonnes. Inclut la gestion des permissions et le téléchargement sécurisé.
**Usage :** Comprendre les fonctionnalités d'export et de reporting.

## Organisation des Fichiers

```
database/diagrams/
├── index.md                          # Ce fichier
├── class_diagram.md                  # Diagramme principal des entités métier
├── global_class_diagram.md           # Vue globale complète du système
├── permissions_class_diagram.md      # Architecture du système de permissions
├── authentication_sequence.md        # Processus d'authentification
├── meeting_creation_sequence.md      # Création de rendez-vous
└── data_export_sequence.md          # Export de données
```

## Conventions Utilisées

### Notation Mermaid
Tous les diagrammes utilisent la syntaxe Mermaid pour assurer la compatibilité avec GitHub et les outils de documentation modernes.

### Légendes des Relations
- `||--||` : Relation un-à-un
- `||--o{` : Relation un-à-plusieurs
- `}o--o{` : Relation plusieurs-à-plusieurs
- `..>` : Dépendance ou utilisation

### Codes Couleur
- **Bleu** (`userClass`) : Entités liées aux utilisateurs et organisations
- **Violet** (`meetingClass`) : Entités liées aux rendez-vous et planification
- **Orange** (`permissionClass`) : Système de permissions et sécurité
- **Vert** (`systemClass`) : Classes utilitaires et services

## Guide d'Utilisation

### Pour les Développeurs
1. Commencez par le **Diagramme de Classes Principal** pour comprendre la structure générale
2. Consultez le **Diagramme Global** pour les détails d'implémentation
3. Référez-vous aux **Diagrammes de Séquence** pour comprendre les processus métier

### Pour les Architectes
1. **Diagramme Global** pour l'architecture complète
2. **Système de Permissions** pour la sécurité
3. **Diagrammes de Séquence** pour les flux critiques

### Pour les Analystes Métier
1. **Diagrammes de Séquence** pour comprendre les processus utilisateur
2. **Diagramme de Classes Principal** pour les entités métier
3. **Authentification** et **Création de Rendez-vous** pour les flux principaux

## Maintenance

Ces diagrammes doivent être mis à jour lors de :
- Ajout de nouvelles entités ou relations
- Modification des processus métier
- Évolution du système de permissions
- Changements dans les flux d'authentification

## Outils Recommandés

- **VS Code** avec l'extension Mermaid Preview
- **GitHub** pour la visualisation intégrée
- **Mermaid Live Editor** pour les modifications rapides
- **PlantUML** en alternative pour les diagrammes complexes
