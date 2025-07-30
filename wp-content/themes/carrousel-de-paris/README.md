# Carrousel de Paris WordPress Theme

## Description
Un thème WordPress personnalisé pour le Carrousel de Paris, inspiré par l'héritage de Joséphine Baker et le cabaret parisien traditionnel. Ce thème a été migré et optimisé depuis une version HTML statique vers une solution WordPress complète.

## Fonctionnalités

### Design et Interface
- **Design responsive** adapté aux mobiles, tablettes et ordinateurs
- **Galerie interactive** avec lightbox et carrousel mobile
- **Animations et effets visuels** pour une expérience immersive
- **Thème sombre** avec accents dorés et effets de lueur

### Fonctionnalités WordPress
- **Support complet des fonctionnalités WordPress**
  - Menus personnalisés
  - Widgets
  - Images à la une
  - Galeries
  - Commentaires
- **Optimisé pour le SEO**
- **Templates spécialisés** :
  - Page d'accueil (`front-page.php`)
  - Blog et archives (`index.php`)
  - Pages individuelles (`page.php`)
  - Articles individuels (`single.php`)
  - Résultats de recherche (`search.php`)
  - Page 404 (`404.php`)

### Personnalisation via l'Outil de Personnalisation
- **Informations de contact** (téléphone, email)
- **Contenu de la section À propos**
- **Liens des réseaux sociaux** (Facebook, Instagram, Twitter, YouTube)
- **Logo et arrière-plan personnalisés**

### Formulaire de Contact
- **Formulaire intégré** avec validation côté serveur
- **Envoi d'emails automatique** à l'administrateur
- **Email de confirmation** automatique pour l'utilisateur
- **Protection contre le spam** avec nonces WordPress

### Performance et Sécurité
- **Optimisations de performance** :
  - Suppression des scripts inutiles
  - Versioning des assets
  - Chargement optimisé des ressources
- **En-têtes de sécurité** ajoutés
- **Échappement et sanitisation** de toutes les données

## Structure des Fichiers

```
carrousel-de-paris/
├── css/
│   ├── variables.css      # Variables CSS personnalisées
│   ├── base.css          # Styles de base
│   ├── header.css        # Styles du header
│   ├── gallery.css       # Styles de la galerie
│   ├── forms.css         # Styles des formulaires
│   ├── footer.css        # Styles du footer
│   ├── blog.css          # Styles du blog
│   ├── mobile.css        # Styles responsive
│   └── ...
├── js/
│   └── main.js           # JavaScript principal
├── images/
│   ├── gallery/          # Images de la galerie
│   └── ...
├── functions.php         # Fonctions PHP du thème
├── style.css            # Fichier principal CSS
├── index.php            # Template principal
├── front-page.php       # Template de la page d'accueil
├── header.php           # En-tête du site
├── footer.php           # Pied de page
├── search.php           # Template de recherche
├── searchform.php       # Formulaire de recherche
└── README.md            # Cette documentation
```

## Installation

1. **Télécharger le thème** dans `/wp-content/themes/`
2. **Activer le thème** depuis l'administration WordPress
3. **Configurer les paramètres** dans Apparence > Personnaliser
4. **Créer les menus** dans Apparence > Menus
5. **Configurer les widgets** si nécessaire

## Configuration

### Menus
Le thème supporte trois emplacements de menus :
- **Menu principal** (`primary`)
- **Menu du pied de page** (`footer`)
- **Liens sociaux** (`social`)

### Zones de Widgets
- **Zone de pied de page 1** (`footer-1`)
- **Zone de pied de page 2** (`footer-2`)
- **Zone de pied de page 3** (`footer-3`)

### Personalisation
Rendez-vous dans **Apparence > Personnaliser** pour configurer :
- Informations de contact
- Contenu de la section À propos
- Liens des réseaux sociaux
- Logo et couleurs

### Formulaire de Contact
Le formulaire de contact intégré enverra les messages à l'adresse email définie dans **Réglages > Général > Adresse e-mail d'administration**.

## Développement

### Variables CSS
Toutes les couleurs et espacements sont définis dans `css/variables.css` pour faciliter la personnalisation.

### Hooks et Filtres
Le thème utilise les hooks WordPress standards et ajoute ses propres actions pour l'extensibilité.

### Support Multi-langues
Le thème est préparé pour la traduction avec le domaine de texte `carrousel`.

## Compatibilité

- **WordPress** : 5.0+
- **PHP** : 7.4+
- **Navigateurs** : Chrome, Firefox, Safari, Edge (versions récentes)

## Crédits

- **Développé par** : Tzvika Ofek
- **Inspiré par** : L'héritage de Joséphine Baker et le cabaret parisien
- **Bibliothèques utilisées** :
  - Lightbox2 pour la galerie
  - Google Fonts (Big Shoulders Display, Cinzel Decorative)

## Support

Pour toute question ou assistance, contactez l'équipe de développement.

---

*Créé avec passion pour perpétuer l'héritage de Joséphine Baker* ✨
