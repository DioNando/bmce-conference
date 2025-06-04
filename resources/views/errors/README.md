# Error Pages for BMCE Invest Application

Ce dossier contient des pages d'erreur personnalisées pour l'application BMCE Invest.

## Pages d'erreur disponibles

- **401** - Non autorisé (Unauthorized)
- **403** - Interdit (Forbidden)
- **404** - Page non trouvée (Not Found)
- **405** - Méthode non autorisée (Method Not Allowed)
- **419** - Page expirée / CSRF (Page Expired)
- **422** - Erreur de validation (Validation Error)
- **429** - Trop de requêtes (Too Many Requests)
- **500** - Erreur serveur (Server Error)
- **503** - Service indisponible (Service Unavailable)
- **error** - Page d'erreur générique (Generic Error)

## Fonctionnalités

- Design responsive utilisant DaisyUI 5
- Prise en charge multilingue (français/anglais)
- Affichage conditionnel des boutons selon l'état d'authentification
- Animation de chargement lors des transitions de page
- Support pour le mode maintenance

## Test des pages d'erreur

En environnement de développement, vous pouvez accéder aux pages d'erreur via les URL suivantes:

```
/error-test/401 - Page d'erreur 401
/error-test/403 - Page d'erreur 403
/error-test/404 - Page d'erreur 404
/error-test/405 - Page d'erreur 405
/error-test/419 - Page d'erreur 419
/error-test/422 - Page d'erreur 422
/error-test/429 - Page d'erreur 429
/error-test/500 - Page d'erreur 500
/error-test/503 - Page d'erreur 503
```

## Mode maintenance

Pour activer le mode maintenance:

```bash
php artisan down --message="Le site est en maintenance. Nous serons de retour bientôt!"
```

Pour désactiver le mode maintenance:

```bash
php artisan up
```

## Structure

- **app/Exceptions/Handler.php** - Gestionnaire des exceptions personnalisé
- **resources/views/errors/** - Pages d'erreur personnalisées
- **resources/views/layouts/error.blade.php** - Layout pour les pages d'erreur
- **app/View/Components/ErrorLayout.php** - Composant de layout d'erreur

## Notes

- Les pages d'erreur utilisent les classes DaisyUI 5 pour le style
- Les traductions sont stockées dans les fichiers `lang/fr.json` et `lang/en.json`
- Les pages incluent des animations et transitions pour une meilleure expérience utilisateur
