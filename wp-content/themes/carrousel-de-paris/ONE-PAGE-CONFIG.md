# Carrousel de Paris - Configuration Site Une Page

## Vue d'ensemble

Le thème Carrousel de Paris a été configuré comme un **site une page** sans fonctionnalité de blog ni recherche. Toutes les demandes de pages sont redirigées vers la page d'accueil.

## Modifications apportées

### 1. Désactivation du blog
- **index.php** : Redirige vers la page d'accueil au lieu d'afficher les articles
- **single.php** : Redirige vers la page d'accueil au lieu d'afficher les articles individuels
- **archive.php** : Redirige vers la page d'accueil au lieu d'afficher les archives
- **page.php** : Redirige vers la page d'accueil au lieu d'afficher les pages individuelles

### 2. Désactivation de la recherche
- **search.php** : Redirige vers la page d'accueil au lieu d'afficher les résultats de recherche
- **searchform.php** : Affiche un message indiquant que la recherche n'est pas disponible
- **functions.php** : Désactive complètement la fonctionnalité de recherche :
  - Supprime le widget de recherche
  - Redirige toutes les requêtes de recherche
  - Désactive les variables de requête de recherche
  - Supprime la recherche de la barre d'administration

### 3. Autres fonctionnalités désactivées
- **Commentaires** : Désactivés pour tous les types de contenu
- **Flux RSS** : Redirigés vers la page d'accueil
- **Sitemaps** : Désactivés
- **API REST** : Liens supprimés du head

### 4. Redirections
- **404 errors** : Redirigent vers la page d'accueil
- **Toutes les URLs non-homepage** : Redirigent vers la page d'accueil (sauf admin, login, AJAX)

## Contenu du site

Tout le contenu du site est affiché sur la page d'accueil (`front-page.php`) qui comprend :
- En-tête avec animations
- Galerie d'images
- Section À propos
- Formulaire de contact
- Pied de page avec informations de contact

## Personnalisation

Le site peut être personnalisé via l'outil de personnalisation WordPress :
- Informations de contact (téléphone, email)
- Contenu de la section À propos
- Liens des réseaux sociaux
- Logo et arrière-plan personnalisés

## Support technique

Pour toute modification du thème, contactez le développeur. Les modifications de ce fichier pourraient affecter le fonctionnement du site une page.
