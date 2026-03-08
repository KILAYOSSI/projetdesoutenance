# CAHIER DES CHARGES
## KILYSAGRI : Plateforme Agricole Intégrée de Gestion et de Commercialisation des Produits Agricoles

---

**Projet de Fin d'Étude**  
**Licence / Master en Informatique**  
**Année Universitaire : 2024-2025**

---

# TABLE DES MATIÈRES

1. PRÉSENTATION DU PROJET
2. PROBLÉMATIQUE ET CONTEXTE
3. OBJECTIFS DU PROJET
4. MÉTHODOLOGIE DE DÉVELOPPEMENT
5. SPÉCIFICATIONS FONCTIONNELLES
6. SPÉCIFICATIONS TECHNIQUES
7. ARCHITECTURE DU SYSTÈME
8. MODÈLE DE DONNÉES
9. INTERFACES UTILISATEUR
10. SÉCURITÉ ET PERMISSIONS
11. PLANNING ET ÉTAPES DE DÉVELOPPEMENT
12. CONCLUSION ET PERSPECTIVES

---

# 1. PRÉSENTATION DU PROJET

## 1.1 Introduction

Le secteur agricole représente un pilier fondamental de l'économie de nombreux pays en développement. Cependant, les producteurs agricoles font face à de nombreux défis : difficulté d'accès aux marchés, manque d'outils de gestion de leurs exploitations, et faible visibilité de leurs produits. 

**KilysAgri** est une plateforme web agricole intelligente qui vise à résoudre ces problèmes en proposant une solution intégrée combinant :
- Un marché en ligne pour la commercialisation des produits agricoles
- Un système de gestion d'exploitation pour les producteurs
- Un module de suivi cultural pour optimiser les rendements
- Un système de vérification d'identité (KYC) pour garantir la confiance

## 1.2 Nom du Projet

**KILYSAGRI** - Plateforme Agricole Intégrée

## 1.3 Type d'Application

Application Web développées avec le framework **Symfony 6.x**

## 1.4 Équipe de Développement

- Développeur : Équipe KilysAgri
- Encadrant : [Nom de l'encadrant]
- Institution : [Nom de l'établissement]

---

# 2. PROBLÉMATIQUE ET CONTEXTE

## 2.1 Contexte du Projet

L'agriculture constitue le secteur économique le plus important dans les pays en développement, employant plus de 60% de la population active. Cependant, ce secteur fait face à plusieurs problématiques majeures :

### 2.1.1 Problèmes Identifiés

| Problème | Description | Impact |
|----------|-------------|--------|
| **Accès limité au marché** | Les producteurs n'ont pas de canaux de vente directs | Revenus faibles, intermédiaires nombreux |
| **Gestion manuelle des exploitations** | Absence d'outils digitaux | Perte de données, inefficacité |
| **Pas de suivi cultural** | Difficulté à suivre les cycles de culture | Rendements non optimisés |
| **Manque de confiance** | Acheteurs et vendeurs ne se connaissent pas | Transactions difficiles |
| **Pertes post-récolte** | Pas de visibilité sur les stocks | Gaspillage alimentaire |

## 2.2 Problématique

Comment concevoir et développer une plateforme web qui permet aux producteurs agricoles de :
- Commercialiser leurs produits de manière efficace ?
- Gérer leurs exploitations de façon digitale ?
- Suivre leurs cultures et optimiser leurs rendements ?
- Établir une relation de confiance avec les acheteurs ?

## 2.3 Hypothèses de Solution

Notre solution **KilysAgri** propose une plateforme multi-services qui :
1. Crée un marché numérique entre producteurs et acheteurs
2. Offre des outils de gestion d'exploitation
3. Permet le suivi digital des cultures
4. Garantit la vérification d'identité des vendeurs

---

# 3. OBJECTIFS DU PROJET

## 3.1 Objectif Général

Développer une plateforme web agricole complète permettant la commercialisation des produits agricoles et la gestion intelligente des exploitations agricoles.

## 3.2 Objectifs Spécifiques

### 3.2.1 Objectifs Commerciales

| Objectif | Description | Indicateur de succès |
|----------|-------------|---------------------|
| **O1** | Créer un marketplace pour les produits agricoles | Plateforme fonctionnelle avec produits |
| **O2** | Permettre la gestion du panier et des commandes | Flux d'achat complet opérationnel |
| **O3** | Implémenter un système de paiement | Module paiement intégré |
| **O4** | Vérifier l'identité des vendeurs (KYC) | Processus de validation fonctionnel |

### 3.2.2 Objectifs de Gestion

| Objectif | Description | Indicateur de succès |
|----------|-------------|---------------------|
| **O5** | Permettre la création et gestion d'exploitations | CRUD exploitations complet |
| **O6** | Gérer les équipements agricoles | Module équipements opérationnel |
| **O7** | Gérer les stocks avec alertes | Alertes stock fonctionnel |
| **O8** | Suivre les cultures de l'exploitation | Module suivi cultural complet |

### 3.2.3 Objectifs d'Optimisation

| Objectif | Description | Indicateur de succès |
|----------|-------------|---------------------|
| **O9** | Enregistrer et analyser les rendements | Module rendement opérationnel |
| **O10** | Publier des conseils agricoles | Module conseils fonctionnel |
| **O11** | Envoyer des notifications aux utilisateurs | Système de notifications actif |

### 3.2.4 Objectifs Techniques

| Objectif | Description | Indicateur de succès |
|----------|-------------|---------------------|
| **O12** | Implémenter un système d'authentification sécurisé | Login/Register avec vérification email |
| **O13** | Gérer les rôles et permissions | Système RBAC opérationnel |
| **O14** | Développer un panel d'administration | Dashboard admin complet |

---

# 4. MÉTHODOLOGIE DE DÉVELOPPEMENT

## 4.1 Méthodologie Utilisée

Nous adoptons la **méthodologie en cascade (Waterfall)** adaptée au contexte académique :

```
┌─────────────────────────────────────────────────────────────────┐
│                    PHASES DU PROJET                            │
├─────────────────────────────────────────────────────────────────┤
│  Phase 1   │   Analyse des besoins      │   2 semaines        │
├─────────────────────────────────────────────────────────────────┤
│  Phase 2   │   Conception               │   3 semaines        │
├─────────────────────────────────────────────────────────────────┤
│  Phase 3   │   Développement            │   8 semaines        │
├─────────────────────────────────────────────────────────────────┤
│  Phase 4   │   Tests et Intégration    │   2 semaines        │
├─────────────────────────────────────────────────────────────────┤
│  Phase 5   │   Documentation            │   2 semaines        │
├─────────────────────────────────────────────────────────────────┤
│  Phase 6   │   Soutenance               │   1 semaine         │
└─────────────────────────────────────────────────────────────────┘
```

## 4.2 Outils de Développement

| Catégorie | Outil | Version |
|-----------|-------|---------|
| **IDE** | VS Code / PhpStorm | - |
| **Framework** | Symfony | 6.x |
| **Base de données** | MySQL | 8.0 |
| **ORM** | Doctrine | 2.x |
| **Serveur Web** | Apache/Nginx | - |
| **Contrôle de version** | Git | - |
| **Hébergement local** | XAMPP/WAMP | - |

## 4.3 Modèle de Conception

- **MVC** (Model-View-Controller)
- **ORM** pour la gestion de la base de données
- **Twig** pour les templates

---

# 5. SPÉCIFICATIONS FONCTIONNELLES

## 5.1 Diagramme des Cas d'Utilisation

```
                        ┌─────────────────┐
                        │   UTILISATEUR   │
                        └────────┬────────┘
                                 │
           ┌─────────────────────┼─────────────────────┐
           │                     │                     │
           ▼                     ▼                     ▼
┌──────────────────┐   ┌──────────────────┐   ┌──────────────────┐
│  S'INSCRIRE     │   │  SE CONNECTER    │   │  CONSULTER      │
│  S'AUTHENTIFIER │   │  VOTRE PROFIL   │   │  PRODUITS       │
└──────────────────┘   └──────────────────┘   └──────────────────┘
                                 │
           ┌─────────────────────┼─────────────────────┐
           │                     │                     │
           ▼                     ▼                     ▼
┌──────────────────┐   ┌──────────────────┐   ┌──────────────────┐
│   ACHETEUR       │   │    VENDEUR       │   │   ADMINISTRATEUR│
├──────────────────┤   ├──────────────────┤   ├──────────────────┤
│ - Ajouter au     │   │ - Ajouter        │   │ - Valider KYC   │
│   panier         │   │   produits       │   │ - Gérer users   │
│ - Passer         │   │ - Gérer          │   │ - Voir stats    │
│   commande       │   │   commandes      │   │ - Modérer       │
│ - Suivre         │   │ - Mettre à jour │   │   contenu       │
│   commandes      │   │   profil        │   └──────────────────┘
└──────────────────┘   └──────────────────┘
```

## 5.2 Modules Fonctionnels

### 5.2.1 Module Authentication

**Description** : Gestion des utilisateurs et de la sécurité

**Fonctionnalités** :
- Inscription avec vérification email
- Connexion sécurisée
- Récupération de mot de passe
- Vérification par code OTP
- Gestion des rôles utilisateur

**Rôles définis** :
- ROLE_USER (Acheteur)
- ROLE_VENDEUR (Vendeur)
- ROLE_KYC_VALIDATED (Vendeur vérifié)
- ROLE_ADMIN (Administrateur)

### 5.2.2 Module Catalogue Produits

**Description** : Gestion et affichage des produits agricoles

**Fonctionnalités** :
- Liste des produits avec pagination
- Recherche par nom
- Filtrage par catégorie
- Détail produit avec images
- Ajout au panier

**Entités** : Produit, Categorie

### 5.2.3 Module Panier et Commandes

**Description** : Gestion du processus d'achat

**Fonctionnalités** :
- Ajout/retrait de produits
- Modification des quantités
- Calcul du total
- Passation de commande
- Suivi du statut de commande

**Statuts de commande** :
- en_attente
- en_cours
- livré
- annulé

**Entités** : Commande, LigneCommande

### 5.2.4 Module Paiement

**Description** : Gestion des transactions financières

**Fonctionnalités** :
- Enregistrement des paiements
- Suivi des transactions
- Différentes méthodes de paiement
- Référence de transaction

**Entités** : Paiement

### 5.2.5 Module KYC (Know Your Customer)

**Description** : Vérification d'identité des vendeurs

**Fonctionnalités** :
- Soumission des documents d'identité
- Upload de photos (pièce + selfie)
- Validation par administrateur
- Statut de vérification

**Documents acceptés** :
- Carte d'identité nationale
- Passeport
- Permis de conduire

**Entités** : Kyc

### 5.2.6 Module Gestion d'Exploitation

**Description** : Gestion des exploitations agricoles

**Fonctionnalités** :
- Création d'exploitation
- Localisation et superficie
- Type de sol et source d'eau
- Gestion des cultures
- Suivi des équipements

**Entités** : Exploitation

### 5.2.7 Module Suivi Cultural

**Description** : Monitoring des cultures

**Fonctionnalités** :
- Enregistrement des cultures
- Dates de semis et récolte
- Superficie cultivée
- Observations et notes
- Calcul automatique superficie

**Entités** : SuiviCulture

### 5.2.8 Module Rendement

**Description** : Calcul et analyse des rendements

**Fonctionnalités** :
- Enregistrement des quantités collectées
- Unités de mesure
- Dates de récolte
- Analyse des performances

**Entités** : Rendement

### 5.2.9 Module Équipements

**Description** : Gestion du matériel agricole

**Fonctionnalités** :
- Catalogue des équipements
- Type et état
- Date d'achat
- Observations

**Entités** : Equipement

### 5.2.10 Module Stock

**Description** : Gestion des intrants et produits

**Fonctionnalités** :
- Gestion des quantités
- Unités de mesure
- Alerte seuil minimal
- Contrôle date d'expiration

**Entités** : Stock

### 5.2.11 Module Conseils

**Description** : Publication de contenu agricole

**Fonctionnalités** :
- Rédaction d'articles
- Catégories de conseils
- Système de publication
- Compteur de vues

**Entités** : Conseil

### 5.2.12 Module Notifications

**Description** : Communication avec les utilisateurs

**Fonctionnalités** :
- Notifications de commande
- Alertes KYC
- Notifications de paiement
- Marquer comme lu

**Entités** : Notification

### 5.2.13 Module Administration

**Description** : Gestion de la plateforme

**Fonctionnalités** :
- Dashboard administrateur
- Validation KYC
- Gestion des utilisateurs
- Statistiques
- Modération du contenu

**Entités** : Admin

---

# 6. SPÉCIFICATIONS TECHNIQUES

## 6.1 Architecture Technique

```
┌─────────────────────────────────────────────────────────────────┐
│                     CLIENT (NAVIGATEUR)                        │
│                   HTML5 / CSS3 / JavaScript                    │
└────────────────────────────┬────────────────────────────────────┘
                             │ HTTP/HTTPS
┌────────────────────────────┴────────────────────────────────────┐
│                     SERVEUR WEB                                │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              APPLICATION SYMFONY                        │   │
│  │  ┌───────────┐  ┌───────────┐  ┌───────────────────┐   │   │
│  │  │ Controller│  │   Entity  │  │    Repository    │   │   │
│  │  │   (PHP)   │  │  (Doctrine)│  │    (Doctrine)   │   │   │
│  │  └───────────┘  └───────────┘  └───────────────────┘   │   │
│  │  ┌───────────┐  ┌───────────┐  ┌───────────────────┐   │   │
│  │  │  Service  │  │   Form    │  │   Twig Templates │   │   │
│  │  └───────────┘  └───────────┘  └───────────────────┘   │   │
│  └─────────────────────────────────────────────────────────┘   │
└────────────────────────────┬────────────────────────────────────┘
                             │
┌────────────────────────────┴────────────────────────────────────┐
│                  BASE DE DONNÉES (MySQL)                      │
│                    Tables / Relations                          │
└─────────────────────────────────────────────────────────────────┘
```

## 6.2 Structure du Projet

```
KilysAgri/
├── bin/                    # Executables Symfony
├── config/                 # Configuration
│   ├── packages/          # Paquets (doctrine, security, etc.)
│   └── routes/           # Routes
├── migrations/           # Migrations base de données
├── public/              # Fichiers publics
│   ├── css/            # Styles CSS
│   ├── images/         # Images
│   └── uploads/       # Fichiers uploadés
├── src/                 # Code source
│   ├── Controller/    # Contrôleurs
│   ├── Entity/       # Entités ORM
│   ├── Form/         # Formulaires
│   ├── Repository/  # Repositories
│   ├── Security/    # Sécurité
│   └── Service/     # Services
├── templates/         # Templates Twig
├── translations/     # Traductions
├── var/              # Fichiers temporaires
├── vendor/           # Dépendances
├── composer.json     # Configuration Composer
└── symfony.lock      # Verrou Symfony
```

## 6.3 Technologies Utilisées

| Composant | Technologie | Version |
|-----------|-------------|---------|
| Framework PHP | Symfony | 6.x |
| Base de données | MySQL | 8.0 |
| ORM | Doctrine | 2.x |
| Moteur de templates | Twig | 3.x |
| Authentification | Symfony Security | 6.x |
| Gestion des formulaires | Symfony Form | 6.x |
| Validation | Symfony Validator | 6.x |
| Email | Symfony Mailer | 6.x |
| JWT Auth | LexikJWTAuthentication | 2.x |

## 6.4 Configuration Serveur

### Requirements Techniques
- PHP 8.1 ou supérieur
- MySQL 8.0 ou MariaDB 10.5
- Apache 2.4 ou Nginx
- Composer installé
- Extension PHP : pdo_mysql, gd, mbstring, xml

---

# 7. ARCHITECTURE DU SYSTÈME

## 7.1 Modèle MVC

```
┌─────────────────────────────────────────────────────────────────┐
│                      CONTRÔLEUR (Controller)                   │
│  - HomeController                                               │
│  - SecurityController                                           │
│  - ProductController                                            │
│  - PanierController                                             │
│  - CommandeController                                           │
│  - KycController                                                │
│  - VendeurController                                            │
│  - AdminController                                              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                         MODÈLE (Entity)                         │
│  - User, Admin, Produit, Categorie                             │
│  - Commande, LigneCommande, Paiement                          │
│  - Kyc, Exploitation, SuiviCulture                             │
│  - Rendement, Equipement, Stock                               │
│  - Conseil, Notification                                       │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      VUE (Templates Twig)                      │
│  - base.html.twig                                               │
│  - home/, product/, panier/, commande/                         │
│  - kyc/, vendeur/, security/, registration/                   │
└─────────────────────────────────────────────────────────────────┘
```

## 7.2 Flux de Données

```
Utilisateur → Requête HTTP → Route → Controller → Entity (Doctrine)
                                         ↓
                           Template Twig ← Repository
                                         ↓
                              Réponse HTTP (HTML)
```

---

# 8. MODÈLE DE DONNÉES

## 8.1 Schéma Relationnel

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│      USER       │     │     ADMIN       │     │   CATEGORIE    │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id (PK)         │<───<│ user_id (FK)   │     │ id (PK)         │
│ email           │     │ niveau          │     │ nom             │
│ password        │     │ permissions     │     └────────┬────────┘
│ roles (JSON)    │     │ estActif        │              │
│ isVerified      │     │ telephone       │      ┌───────┴───────┐
│ isVendeur       │     └────────┬────────┘      │               │
│ isKycValidated  │              │          ┌───<┴───┐           │
│ nomComplet      │              │          │        │           │
│ telephone       │              │     ┌────┴────┐  │           │
└────────┬────────┘              │     │PRODUIT  │  │           │
         │                       │     ├─────────┤  │           │
    ┌────┴────┐                  │     │ id (PK) │  │           │
    │         │                  │     │ nom     │──┘           │
    │         │    ┌────────────┴────<│ prix    │              │
    │         │    │ KYCC            │ quantite│              │
    │    ┌────┴───┤┌─────────────────┐│ image   │              │
    │    │EXPLOIT ││ id (PK)         ││ user_id │              │
    │    ├────────┤│ utilisateur_id  ││categorie│              │
    │    │id (PK) ││ typePiece       │└────┬────┘              │
    │    │ nom    ││ numeroPiece     │     │                   │
    │    │superfic││ photoRecto     │     │                   │
    │    │localisa││ photoVerso     │     │                   │
    │    │user_id ││ photoSelfie    │     │                   │
    │    └────┬───┘│ status         │     │                   │
    │         │    └──────┬────────┘     │    ┌────────────┐   │
    │    ┌────┴─────┐     │              │    │  COMMANDE  │   │
    │    │SUIVI_CULT│     │              │    ├────────────┤   │
    │    ├──────────┤     │              │    │ id (PK)    │   │
    │    │ id (PK)  │     │              │    │user_id(FK) │   │
    │    │exploitat │     │              │    │montantTotal│   │
    │    │ categorie│     │              │    │ status     │   │
    │    │ nom      │     │              │    │dateCommande│───┼──┐
    │    │ superficie│    │              │    └─────┬──────┘   │  │
    │    │ dateSemis│     │              │          │          │  │
    │    └────┬─────┘     │              │    ┌─────┴─────┐   │  │
    │         │           │              │    │ LIGNECMD  │   │  │
    │    ┌────┴────┐      │              │    ├───────────┤   │  │
    │    │RENDEMENT│      │              │    │ id (PK)   │   │  │
    │    ├─────────┤      │              │    │ commande  │───┘  │
    │    │id (PK)  │      │              │    │ produit   │──┐   │
    │    │suiviCul │──────┘              │    │ quantite  │  │   │
    │    │quantite │                     │    │ prixUnit  │  │   │
    │    └─────────┘                     │    └───────────┘  │   │
    │                                    │                    │   │
    │ ┌─────────────┐ ┌─────────────┐    │    ┌────────────┐  │   │
    │ │ EQUIPEMENT  │ │    STOCK    │    │    │  PAIEMENT  │  │   │
    │ ├─────────────┤ ├─────────────┤    │    ├────────────┤  │   │
    │ │ id (PK)     │ │ id (PK)     │    │    │ id (PK)    │  │   │
    │ │ nom         │ │ nom         │    │    │user_id(FK) │  │   │
    │ │ type        │ │ type        │    │    │commande_id │──┘   │
    │ │ etat        │ │ quantite    │    │    │ montant    │      │
    │ │exploitation │ │exploitation │    │    │ methode    │      │
    │ └─────────────┘ └─────────────┘    │    └────────────┘      │
    │                                      │                       │
    │  ┌─────────────┐  ┌─────────────┐    │  ┌────────────┐        │
    │  │  CONSEIL   │  │NOTIFICATION│    │  │            │        │
    │  ├─────────────┤ ├─────────────┤    │  │            │        │
    │  │ id (PK)     │ │ id (PK)     │────┼──┤            │        │
    │  │ titre       │ │ titre       │    │  │            │        │
    │  │ contenu     │ │ message     │    │  │            │        │
    │  │ auteur_id   │ │ utilisateur │    │  │            │        │
    │  │ estPublie   │ │ lu          │    │  │            │        │
    │  └─────────────┘ └─────────────┘    │  │            │        │
    │                                      │  │            │        │
    └──────────────────────────────────────┴──┴────────────┴────────┘
```

## 8.2 Description des Tables

### 8.2.1 Tables Principales

| Table | Description | Relations |
|-------|-------------|-----------|
| **user** | Utilisateurs de la plateforme | 1:N Produit, Commande, Exploitation |
| **admin** | Administrateurs | 1:1 User |
| **produit** | Produits agricoles | N:1 User, Categorie |
| **categorie** | Catégories de produits | 1:N Produit |
| **commande** | Commandes | N:1 User, 1:N LigneCommande |
| **ligne_commande** | Lignes de commande | N:1 Commande, Produit |
| **paiement** | Paiements | N:1 User, Commande |
| **kyc** | Documents KYC | N:1 User |
| **exploitation** | Exploitations agricoles | N:1 User, 1:N SuiviCulture |

### 8.2.2 Tables Secondaires

| Table | Description | Relations |
|-------|-------------|-----------|
| **suivi_culture** | Suivi des cultures | N:1 Exploitation, Categorie |
| **rendement** | Rendements | N:1 SuiviCulture |
| **equipement** | Équipements | N:1 Exploitation |
| **stock** | Stocks | N:1 Exploitation |
| **conseil** | Conseils agricoles | N:1 User |
| **notification** | Notifications | N:1 User |

---

# 9. INTERFACES UTILISATEUR

## 9.1 Structure des Pages

### 9.1.1 Pages Publiques
- Page d'accueil
- Catalogue produits
- Détail produit
- Inscription
- Connexion

### 9.1.2 Pages Utilisateur
- Mon panier
- Mes commandes
- Statut KYC

### 9.1.3 Pages Vendeur
- Dashboard vendeur
- Gestion produits
- Formulaire produit
- Commandes reçues

### 9.1.4 Pages Administrateur
- Dashboard admin
- Liste KYC à valider
- Validation KYC

## 9.2 Charte Graphique (À Définir)

| Élément | Spécification |
|---------|---------------|
| Couleurs principales | Vert agricultural, Blanc |
| Typographie | Sans-serif (Roboto, Open Sans) |
| Responsive | Bootstrap 5 |
| Icons | Font Awesome |

---

# 10. SÉCURITÉ ET PERMISSIONS

## 10.1 Mesures de Sécurité

### 10.1.1 Authentication
- Hachage des mots de passes avec **bcrypt** (cost: 12)
- Vérification par **email** avec code de confirmation
- **OTP** à 6 chiffres avec expiration

### 10.1.2 Autorisation
- Système de **rôles** (RBAC)
- **Permissions** par rôle
- **Access Control** dans firewall

### 10.1.3 Protection
- **CSRF** tokens sur tous les formulaires
- **Validation** des données utilisateur
- **Sanitization** des entrées

## 10.2 Matrice des Permissions

| Fonctionnalité | USER | VENDEUR | KYC_VALIDATED | ADMIN |
|----------------|------|---------|----------------|-------|
| Voir produits | ✓ | ✓ | ✓ | ✓ |
| Ajouter au panier | ✓ | ✓ | ✓ | ✓ |
| Passer commande | ✓ | ✓ | ✓ | ✓ |
| Ajouter produits | ✗ | ✗ | ✓ | ✓ |
| Valider KYC | ✗ | ✗ | ✗ | ✓ |
| Gérer utilisateurs | ✗ | ✗ | ✗ | ✓ |
| Voir statistiques | ✗ | ✗ | ✗ | ✓ |

## 10.3 Routes Protégées

```yaml
access_control:
  - { path: ^/login, roles: PUBLIC_ACCESS }
  - { path: ^/register, roles: PUBLIC_ACCESS }
  - { path: ^/produits, roles: PUBLIC_ACCESS }
  - { path: ^/panier, roles: ROLE_USER }
  - { path: ^/commande, roles: ROLE_USER }
  - { path: ^/vendeur, roles: ROLE_USER }
  - { path: ^/kyc/admin, roles: ROLE_ADMIN }
```

---

# 11. PLANNING ET ÉTAPES DE DÉVELOPPEMENT

## 11.1 Chronogramme

```
SEMAINE 1  2  3  4  5  6  7  8  9  10 11 12 13 14 15 16
          │  │  │  │  │  │  │  │  │  │  │  │  │  │  │
ANALYSE   ██████
CONCEPTION      █████████
DÉVELOPPEMENT            ████████████████████
TESTS                              ██████
DOCUMENTATION                              ██████
SOUTENANCE                                      ██
```

## 11.2 Détail des Livrables

| Phase | Livrable | Date |
|-------|----------|------|
| Analyse | Cahier des charges | S2 |
| Conception | MCD, MLD, Schéma | S4 |
| Développement | Application fonctionnelle | S12 |
| Tests | Rapport de tests | S14 |
| Documentation | Manuel utilisateur | S15 |
| Présentation | Soutenance | S16 |

## 11.3 Critères de Succès

- [ ] Inscription et authentification fonctionnels
- [ ] Catalogue produits avec catégories
- [ ] Système de panier et commandes
- [ ] Processus KYC opérationnel
- [ ] Gestion des exploitations
- [ ] Suivi cultural implémenté
- [ ] Panel administrateur complet
- [ ] Tests validés
- [ ] Documentation rédigée

---

# 12. CONCLUSION ET PERSPECTIVES

## 12.1 Synthèse

Ce projet **KilysAgri** propose une solution complète pour moderniser le secteur agricole en offrant :
1. Un **marché en ligne** pour écouler les produits
2. Des **outils de gestion** pour les exploitations
3. Un **suivi digital** des cultures
4. Une **confiance** entre acteurs via KYC

## 12.2 Perspectives d'Évolution

### Court Terme (3 mois)
- Application mobile (API REST)
- Intégration paiement mobile (Orange Money, MTN)
- Amélioration du design UI/UX

### Moyen Terme (6 mois)
- Module de chat en direct
- Système de fidélisation
- Programme de points

### Long Terme (1 an)
- Intelligence artificielle pour conseils
- Analyse prédictive des rendements
- Partenariats avec institutions agricoles

## 12.3 Impacts Attendus

| Impact | Description |
|--------|-------------|
| **Économique** | Augmentation des revenus des producteurs |
| **Social** | Création d'emplois dans le numérique agricole |
| **Technologique** | Modernisation du secteur agricole |
| **Sécuritaire** | Transactions fiabilisées par KYC |

---

# ANNEXES

## Annexe A : Liste des Contrôleurs

| Contrôleur | Fonctionnalité |
|------------|----------------|
| HomeController | Page d'accueil |
| SecurityController | Connexion/Déconnexion |
| RegistrationController | Inscription |
| ProductController | Catalogue produits |
| PanierController | Gestion panier |
| CommandeController | Gestion commandes |
| KycController | Vérification identité |
| VendeurController | Espace vendeur |
| NotificationController | Notifications |

## Annexe B : Liste des Entités

| Entité | Description |
|--------|-------------|
| User | Utilisateur |
| Admin | Administrateur |
| Produit | Produit agricole |
| Categorie | Catégorie de produit |
| Commande | Commande client |
| LigneCommande | Ligne de commande |
| Paiement | Paiement |
| Kyc | Vérification identité |
| Exploitation | Exploitation agricole |
| SuiviCulture | Suivi cultural |
| Rendement | Rendement agricole |
| Equipement | Équipement |
| Stock | Stock |
| Conseil | Conseil agricole |
| Notification | Notification |

## Annexe C : Dépendances Composer

```json
{
    "symfony/skeleton": "^6.0",
    "symfony/framework-bundle": "^6.0",
    "symfony/security-bundle": "^6.0",
    "symfony/form": "^6.0",
    "symfony/validator": "^6.0",
    "symfony/mailer": "^6.0",
    "doctrine/orm": "^2.12",
    "doctrine/doctrine-bundle": "^2.7",
    "lexik/jwt-authentication-bundle": "^2.16",
    "twig/twig": "^3.0"
}
```

---

**Document élaboré par :**  
Équipe KilysAgri

**Date :** Mars 2025

**Version :** 1.0

**Signature :** ________________________

---

*Fin du Cahier des Charges*

