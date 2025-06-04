# Documentation du Système de Diagrammes Mermaid

## Introduction

Le système de diagrammes Mermaid intégré à l'application BMCE Invest permet de visualiser et de télécharger différents types de diagrammes représentant l'architecture et les fonctionnalités du projet. Cette documentation explique comment utiliser, visualiser et télécharger les diagrammes, ainsi que la façon d'en créer de nouveaux.

## Types de Diagrammes Disponibles

L'application propose quatre types de diagrammes :

1. **Diagrammes de Classes** - Représentent la structure des classes du projet et leurs relations.
2. **Diagrammes de Séquences** - Illustrent les interactions entre les différents objets et acteurs du système.
3. **Diagrammes de Packages** - Montrent l'organisation des packages et la structure du projet.
4. **Diagrammes de Cas d'Utilisation** - Visualisent les fonctionnalités du système du point de vue des utilisateurs.

## Accès aux Diagrammes

Les diagrammes sont accessibles via le menu d'administration. Pour y accéder :

1. Connectez-vous à l'application BMCE Invest avec un compte administrateur.
2. Dans la barre de navigation, cliquez sur "Diagrammes".
3. Vous serez redirigé vers la page d'index des diagrammes où vous pourrez choisir le type de diagramme à visualiser.

## Visualisation des Diagrammes

Chaque type de diagramme est présenté dans sa propre page dédiée :

- `/admin/diagrams/classes` pour les diagrammes de classes
- `/admin/diagrams/sequences` pour les diagrammes de séquences
- `/admin/diagrams/packages` pour les diagrammes de packages
- `/admin/diagrams/use-cases` pour les diagrammes de cas d'utilisation

Les diagrammes sont rendus à l'aide de la bibliothèque Mermaid.js qui transforme la syntaxe textuelle des diagrammes en représentations visuelles SVG.

## Téléchargement des Diagrammes

Chaque diagramme peut être téléchargé au format PNG :

1. Visualisez le diagramme souhaité
2. Cliquez sur le bouton "Télécharger PNG" situé en haut à droite du diagramme
3. Le fichier PNG sera téléchargé automatiquement sur votre appareil avec un nom au format `diagram-mermaid-[ID].png`

## Structure Technique

### Composant Mermaid

Le composant `<x-diagram.mermaid>` est au cœur du système de rendu des diagrammes. Ce composant prend trois propriétés :

- `id` (optionnel) : Un identifiant unique pour le diagramme (généré automatiquement si non fourni)
- `definition` (obligatoire) : La définition textuelle du diagramme en syntaxe Mermaid
- `title` (optionnel) : Le titre à afficher au-dessus du diagramme

Exemple d'utilisation :

```php
<x-diagram.mermaid 
    title="Diagramme de Classes Utilisateur"
    definition="classDiagram
        class User {
            +String name
            +String email
        }
        class Role {
            +String name
        }
        User -- Role
    "
/>
```

### Contrôleur

Le système utilise `DiagramController` qui contient cinq méthodes principales :

1. `index()` : Affiche la page d'index avec les liens vers tous les types de diagrammes
2. `classes()` : Affiche les diagrammes de classes
3. `sequences()` : Affiche les diagrammes de séquences
4. `packages()` : Affiche les diagrammes de packages
5. `useCases()` : Affiche les diagrammes de cas d'utilisation

### Routes

Les routes suivantes sont définies dans `routes/web.php` :

```php
Route::get('/diagrams', [DiagramController::class, 'index'])->name('diagrams.index');
Route::get('/diagrams/classes', [DiagramController::class, 'classes'])->name('diagrams.classes');
Route::get('/diagrams/sequences', [DiagramController::class, 'sequences'])->name('diagrams.sequences');
Route::get('/diagrams/packages', [DiagramController::class, 'packages'])->name('diagrams.packages');
Route::get('/diagrams/use-cases', [DiagramController::class, 'useCases'])->name('diagrams.use-cases');
```

## Création de Nouveaux Diagrammes

Pour ajouter un nouveau diagramme dans une page existante :

1. Ouvrez le fichier Blade correspondant au type de diagramme (`resources/views/admin/diagrams/`.
2. Ajoutez un nouveau bloc `<x-diagram.mermaid>` avec les propriétés appropriées.
3. Définissez le contenu du diagramme en utilisant la syntaxe Mermaid.

Pour créer un nouveau type de diagramme :

1. Créez une nouvelle méthode dans `DiagramController.php`
2. Ajoutez une nouvelle route dans `web.php`
3. Créez une nouvelle vue Blade dans le dossier `resources/views/admin/diagrams/`
4. Utilisez le composant `<x-diagram.mermaid>` pour afficher vos diagrammes

## Syntaxe Mermaid

Voici quelques exemples de syntaxe pour différents types de diagrammes :

### Diagramme de Classes

```
classDiagram
    class User {
        +String name
        +String email
        +isAdmin()
    }
    class Role {
        +String name
    }
    User -- Role
```

### Diagramme de Séquences

```
sequenceDiagram
    actor U as User
    participant S as System
    U->>S: Login Request
    S->>U: Authentication Response
```

### Diagramme de Packages

```
graph TD
    App[App]
    Models[Models]
    App --> Models
    Models --> User[User.php]
```

### Diagramme de Cas d'Utilisation

```
graph TD
    Admin((Administrateur))
    CreateUser[Créer un utilisateur]
    Admin --> CreateUser
```

## Ressources Additionnelles

- [Documentation officielle de Mermaid](https://mermaid.js.org/intro/)
- [Éditeur en ligne Mermaid](https://mermaid.live/)

## Support Technique

Pour toute question ou problème concernant les diagrammes, veuillez contacter l'équipe de développement.
