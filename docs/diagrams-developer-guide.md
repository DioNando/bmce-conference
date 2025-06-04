# Guide du Développeur : Modification et Création de Diagrammes

Ce guide est destiné aux développeurs qui souhaitent modifier les diagrammes existants ou en créer de nouveaux dans l'application BMCE Invest.

## Architecture du Système de Diagrammes

Le système de diagrammes repose sur :

1. **Composant Blade `<x-diagram.mermaid>`** - Situé dans `resources/views/components/diagram/mermaid.blade.php`
2. **Contrôleur `DiagramController`** - Situé dans `app/Http/Controllers/Admin/DiagramController.php`
3. **Vues Blade** - Situées dans `resources/views/admin/diagrams/`
4. **Routes** - Définies dans `routes/web.php`
5. **Bibliothèque Mermaid.js** - Chargée depuis CDN

## Modification d'un Diagramme Existant

Pour modifier un diagramme existant :

1. Identifiez le fichier Blade concerné dans `resources/views/admin/diagrams/`
2. Localisez le bloc `<x-diagram.mermaid>` correspondant au diagramme à modifier
3. Modifiez la propriété `definition` qui contient la syntaxe Mermaid

Exemple de modification :

```php
<!-- Avant -->
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

<!-- Après -->
<x-diagram.mermaid 
    title="Diagramme de Classes Utilisateur Amélioré"
    definition="classDiagram
        class User {
            +String name
            +String email
            +String phone
            +isAdmin()
        }
        class Role {
            +String name
            +String[] permissions
        }
        User -- Role : has
    "
/>
```

## Ajout d'un Nouveau Diagramme

Pour ajouter un nouveau diagramme dans une page existante :

1. Ouvrez le fichier Blade correspondant au type de diagramme
2. Ajoutez un nouveau bloc `<x-diagram.mermaid>` avec les propriétés appropriées

Exemple d'ajout :

```php
<x-diagram.mermaid 
    title="Nouveau Diagramme"
    definition="classDiagram
        class NewClass {
            +String property
            +method()
        }
    "
/>
```

## Création d'une Nouvelle Page de Diagrammes

Pour créer un type de diagramme entièrement nouveau :

1. **Ajoutez une méthode** dans `DiagramController.php` :

```php
public function newDiagramType()
{
    return view('admin.diagrams.new-diagram-type');
}
```

2. **Ajoutez une route** dans `web.php` :

```php
Route::get('/diagrams/new-diagram-type', [DiagramController::class, 'newDiagramType'])
    ->name('diagrams.new-diagram-type');
```

3. **Créez une vue Blade** dans `resources/views/admin/diagrams/new-diagram-type.blade.php` :

```php
<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __("Dashboard") }}</a></li>
                <li><a href="{{ route('admin.diagrams.index') }}" class="text-primary">{{ __("Diagrammes") }}</a></li>
                <li>{{ __("Nouveau Type de Diagramme") }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __("Nouveau Type de Diagramme") }}
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
            title="Titre du Nouveau Diagramme"
            definition="
                // Contenu du diagramme en syntaxe Mermaid
            "
        />
    </section>
</x-app-layout>
```

4. **Ajoutez un lien** vers cette nouvelle page dans `resources/views/admin/diagrams/index.blade.php` :

```php
<div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
    <div class="card-body">
        <h2 class="card-title">{{ __("Nouveau Type de Diagramme") }}</h2>
        <p>{{ __("Description du nouveau type de diagramme") }}</p>
        <div class="card-actions justify-end mt-4">
            <a href="{{ route('admin.diagrams.new-diagram-type') }}" class="btn btn-primary">
                <x-heroicon-s-cube class="w-4 h-4 mr-2" />
                {{ __("Voir") }}
            </a>
        </div>
    </div>
</div>
```

## Syntaxe Mermaid Avancée

### Diagramme de Classes

```
classDiagram
    class User {
        +String name
        +String email
        -String password
        #Int age
        +isAdmin() bool
    }
    
    class Organization {
        +String name
        +User[] users
    }
    
    User "n" -- "1" Organization : belongs to
    
    class UserType {
        <<enumeration>>
        ADMIN
        INVESTOR
        ISSUER
    }
    
    User *-- UserType : has
```

### Diagramme de Séquences

```
sequenceDiagram
    actor A as Admin
    participant S as System
    participant DB as Database
    participant E as Email Service
    
    A->>S: Create new user
    S->>DB: Insert user data
    alt Success
        DB->>S: User created
        S->>E: Send welcome email
        E->>S: Email sent
        S->>A: User created successfully
    else Error
        DB->>S: Error
        S->>A: Error creating user
    end
```

### Diagramme de Flux (Flowchart)

```
graph TD
    A[Start] --> B{Is user logged in?}
    B -->|Yes| C[Dashboard]
    B -->|No| D[Login Page]
    D --> E[Enter Credentials]
    E --> F{Valid Credentials?}
    F -->|Yes| C
    F -->|No| D
```

### Diagramme d'État (State Diagram)

```
stateDiagram-v2
    [*] --> Draft
    Draft --> Pending: Submit
    Pending --> Confirmed: Approve
    Pending --> Rejected: Reject
    Confirmed --> Completed: Complete
    Confirmed --> Cancelled: Cancel
    Rejected --> [*]
    Completed --> [*]
    Cancelled --> [*]
```

## Bonnes Pratiques

1. **Nomenclature cohérente** : Utilisez les mêmes noms pour les entités dans tous les diagrammes.
2. **Diagrammes concis** : Limitez la taille des diagrammes pour qu'ils restent lisibles.
3. **Organisation** : Regroupez les diagrammes liés dans la même page.
4. **Documentation** : Ajoutez des titres et descriptions clairs pour chaque diagramme.
5. **Tests** : Vérifiez que votre syntaxe Mermaid est correcte en utilisant [l'éditeur en ligne Mermaid](https://mermaid.live/) avant de l'ajouter à l'application.

## Dépannage

### Problème : Le diagramme ne s'affiche pas

1. Vérifiez la syntaxe Mermaid dans un éditeur en ligne
2. Assurez-vous que la bibliothèque Mermaid est bien chargée
3. Vérifiez les erreurs dans la console du navigateur

### Problème : Le téléchargement PNG ne fonctionne pas

1. Vérifiez que le SVG est correctement généré
2. Assurez-vous que le paramètre `securityLevel: 'loose'` est présent dans l'initialisation de Mermaid
3. Vérifiez les erreurs dans la console du navigateur

## Ressources

- [Documentation officielle Mermaid](https://mermaid.js.org/intro/)
- [Cheat sheet Mermaid](https://mermaid.js.org/syntax/flowchart.html)
- [Éditeur en ligne Mermaid](https://mermaid.live/)
- [GitHub de Mermaid](https://github.com/mermaid-js/mermaid)
