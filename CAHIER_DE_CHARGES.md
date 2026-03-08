# CAHIER DES CHARGES - KILYSAGRI

## Plateforme Agricole Intégrée

**Version:** 1.0  
**Date:** 2025  
**Type de projet:** Application Web Symfony (E-commerce & Gestion Agricole)  
**Développeur:** Équipe KilysAgri

---

## 1. PRÉSENTATION DU PROJET

### 1.1 Contexte et Objectifs

KilysAgri est une plateforme agricole intelligente qui combine :
- Un **marché en ligne** pour la vente de produits agricoles
- Un **système de gestion d'exploitation** pour les producteurs
- Un **suivi cultural** pour optimiser les rendements
- Une **place de marché multi-vendeurs** avec vérification d'identité (KYC)

### 1.2 Public Cible

| Segment | Description |
|---------|-------------|
| **Producteurs/Vendeurs** | Agriculteurs souhaitant vendre leurs produits et gérer leur exploitation |
| **Acheteurs** | Particuliers, restaurants,revendeurs achétant des produits agricoles |
| **Administrateurs** | Équipe de gestion de la plateforme |

---

## 2. SPÉCIFICATIONS FONCTIONNELLES

### 2.1 Module Authentication et Utilisateurs

#### 2.1.1 Inscription Utilisateur
- **Champs requis :**
  - Email (unique, valide)
  - Mot de passe (hashé bcrypt)
  - Nom complet
  - Téléphone
- **Fonctionnalités :**
  - Vérification par email avec code de confirmation
  - Code OTP à 6 chiffres avec expiration (délai configurable)
  - Rôle par défaut : `ROLE_USER`

#### 2.1.2 Connexion
- **Méthode :** Formulaire login (email + mot de passe)
- **Sécurité :** Protection par firewall Symfony
- **Redirection par défaut :** Page des produits

#### 2.1.3 Rôles et Permissions

| Rôle | Description | Permissions |
|------|-------------|-------------|
| `ROLE_USER` | Utilisateur standard | Acheter, consulter,_notifications |
| `ROLE_VENDEUR` | Vendeur de produits | Ajouter produits, gérer commandes, voir statistiques |
| `ROLE_KYC_VALIDATED` | Vendeur vérifié | Vente active après validation KYC |
| `ROLE_ADMIN` | Administrateur | Gestion totale, validation KYC, statistiques |
| `ROLE_SUPER_ADMIN` | Super Admin | Accès complet système |

### 2.2 Module Catalogue Produits

#### 2.2.1 Entité Produit
```
Produit {
  - id: Integer (PK, auto-increment)
  - nom: String(255) [required]
  - description: Text
  - prix: Decimal(10,2) [required]
  - quantite: Integer [required]
  - image: String(255)
  - utilisateur: User (FK) [ManyToOne]
  - categorie: Categorie (FK) [ManyToOne]
  - createdAt: DateTime
  - updatedAt: DateTime (nullable)
}
```

#### 2.2.2 Catégories de Produits
- Gestion des catégories (CRUD)
- Association produits-catégories
- Exemples : Légumes, Fruits, Céréales, Épices, etc.

#### 2.2.3 Fonctionnalités
- Liste des produits (paginée)
- Recherche et filtrage par catégorie
- Détail produit avec image
- Ajout au panier

### 2.3 Module Panier et Commandes

#### 2.3.1 Gestion du Panier
- Ajout de produits avec quantité
- Modification des quantités
- Suppression de produits
- Vidage du panier
- Calcul automatique du total

#### 2.3.2 Passation de Commande
```
Commande {
  - id: Integer (PK)
  - utilisateur: User (FK) [ManyToOne]
  - ligneCommandes: Collection<LigneCommande> [OneToMany]
  - montantTotal: Decimal(10,2) [default: 0]
  - status: String [enum: en_attente, en_cours, livré, annulé]
  - dateCommande: DateTime
}

LigneCommande {
  - id: Integer (PK)
  - commande: Commande (FK) [ManyToOne]
  - produit: Produit (FK) [ManyToOne]
  - quantite: Integer
  - prixUnitaire: Decimal(10,2)
}
```

#### 2.3.3 Statuts de Commande
| Statut | Description |
|--------|-------------|
| `en_attente` | Commande en attente de traitement |
| `en_cours` | Commande en cours de préparation |
| `livré` | Commande livrée au client |
| `annulé` | Commande annulée |

#### 2.3.4 Suivi des Commandes
- Liste des commandes utilisateur
- Détails d'une commande
- Historique des statuts

### 2.4 Module Paiement

#### 2.4.1 Entité Paiement
```
Paiement {
  - id: Integer (PK)
  - utilisateur: User (FK) [ManyToOne]
  - commande: Commande (FK) [ManyToOne]
  - montant: Decimal(10,2) [required]
  - statut: String [en_attente, validé, échoué]
  - methode: String(50) [required]
  - reference: String(255)
  - transactionId: String(255)
  - createdAt: DateTime
  - datePaiement: DateTime (nullable)
}
```

### 2.5 Module KYC (Vérification d'Identité)

#### 2.5.1 Objectif
Valider l'identité des vendeurs avant toute activité commerciale.

#### 2.5.2 Entité Kyc
```
Kyc {
  - id: Integer (PK)
  - utilisateur: User (FK) [ManyToOne]
  - typePiece: String [cni, passeport, permis_conduire]
  - numeroPiece: String(100) [required]
  - photoPieceRecto: String(255)
  - photoPieceVerso: String(255)
  - photoSelfie: String(255)
  - status: String [enum: en_attente, validé, rejeté]
  - createdAt: DateTime
}
```

#### 2.5.3 Flux KYC
1. Utilisateur soumet sa demande KYC
2. Upload des documents (pièce recto/verso + selfie)
3. Statut initial : `en_attente`
4. Administrateur valide ou rejette
5. Si validé : `isKycValidated = true` sur User

#### 2.5.4 Permissions Vendeur
```php
function canSell(): bool {
    return $this->isVendeur && $this->isKycValidated;
}
```

### 2.6 Module Gestion d'Exploitation

#### 2.6.1 Entité Exploitation
```
Exploitation {
  - id: Integer (PK)
  - nom: String(255) [required]
  - superficie: Decimal(10,2)
  - localisation: String(255)
  - typeSol: String(100)
  - sourceEau: String(100)
  - statut: String(50)
  - utilisateur: User (FK) [ManyToOne]
  - suiviCultures: Collection<SuiviCulture> [OneToMany]
  - equipements: Collection<Equipement> [OneToMany]
  - stocks: Collection<Stock> [OneToMany]
  - createdAt: DateTime
}
```

#### 2.6.2 Fonctionnalités
- Création/modification d'exploitations
- Suivi des cultures
- Gestion des équipements
- Gestion des stocks
- Calcul automatique de la superficie cultivée

### 2.7 Module Suivi Cultural

#### 2.7.1 Entité SuiviCulture
```
SuiviCulture {
  - id: Integer (PK)
  - exploitation: Exploitation (FK) [ManyToOne]
  - categorie: Categorie (FK) [ManyToOne]
  - nom: String(255) [required]
  - variete: String(100)
  - superficie: Decimal(10,2)
  - dateSemis: Date
  - dateRecoltePrevue: Date
  - dateRecolteReelle: Date
  - statut: String(50) [en_cours, récolté, annulé]
  - observations: Text
  - createdAt: DateTime
}
```

#### 2.7.2 Fonctionnalités
- Enregistrement des cultures
- Suivi du cycle de culture
- Dates de semis et récolte
- Statut de culture (en cours, récolté, annulé)
- Observations et notes

### 2.8 Module Rendement

#### 2.8.1 Entité Rendement
```
Rendement {
  - id: Integer (PK)
  - suiviCulture: SuiviCulture (FK) [ManyToOne]
  - quantite: Decimal(10,2)
  - unite: String(50)
  - dateRecolte: Date
  - observations: Text
  - createdAt: DateTime
}
```

### 2.9 Module Équipements

#### 2.9.1 Entité Equipement
```
Equipement {
  - id: Integer (PK)
  - nom: String(255) [required]
  - type: String(100) [required]
  - etat: String(50) [bon, moyen, mauvais]
  - dateAchat: Date
  - exploitation: Exploitation (FK) [ManyToOne]
  - observations: Text
  - createdAt: DateTime
}
```

### 2.10 Module Gestion des Stocks

#### 2.10.1 Entité Stock
```
Stock {
  - id: Integer (PK)
  - nom: String(255) [required]
  - type: String(100) [required]
  - quantite: Decimal(10,2) [required]
  - unite: String(50) [required]
  - seuilAlerte: Decimal(10,2)
  - exploitation: Exploitation (FK) [ManyToOne]
  - dateExpiration: Date
  - observations: Text
  - createdAt: DateTime
}
```

#### 2.10.2 Fonctionnalités
- Alerte si stock < seuilAlerte
- Contrôle des dates d'expiration
- Historique des mouvements de stock

### 2.11 Module Conseils Agricoles

#### 2.11.1 Entité Conseil
```
Conseil {
  - id: Integer (PK)
  - titre: String(255) [required]
  - contenu: Text [required]
  - type: String(50) [required]
  - categorie: String(100)
  - image: String(255)
  - auteur: User (FK) [ManyToOne]
  - estPublie: Boolean
  - datePublication: DateTime
  - vues: Integer
}
```

#### 2.11.2 Fonctionnalités
- Publication d'articles/conseils
- Catégories (Semis, Récolte, Entretien, etc.)
- Compteur de vues
- Système de publication (modération)

### 2.12 Module Notifications

#### 2.12.1 Entité Notification
```
Notification {
  - id: Integer (PK)
  - titre: String(255) [required]
  - message: Text [required]
  - type: String(50) [required]
  - lu: Boolean [default: false]
  - utilisateur: User (FK) [ManyToOne]
  - createdAt: DateTime
  - readAt: DateTime (nullable)
}
```

#### 2.12.2 Types de Notifications
- Commande passée
- Paiement validé
- KYC accepté/rejeté
- Nouveau message
- Alerte stock

### 2.13 Module Administrateur

#### 2.13.1 Entité Admin
```
Admin {
  - id: Integer (PK)
  - user: User (FK) [OneToOne, required]
  - niveau: String(50) [super_admin, admin, moderateur]
  - permissions: JSON
  - estActif: Boolean
  - telephone: String(255)
  - adresse: String(255)
  - dateNomination: DateTime
  - derniereConnexion: DateTime
}
```

#### 2.13.2 Niveaux et Permissions

| Niveau | Permissions |
|--------|-------------|
| `super_admin` | Toutes permissions |
| `admin` | Gestion utilisateurs, produits, commandes, KYC |
| `moderateur` | Validation contenu, gestion conseils |

#### 2.13.3 Fonctionnalités Admin
- Tableau de bord
- Gestion des utilisateurs
- Validation KYC
- Gestion des commandes
- Statistiques

---

## 3. SPÉCIFICATIONS TECHNIQUES

### 3.1 Architecture

```
KilysAgri/
├── config/              # Configuration Symfony
│   ├── packages/       # Paquets (Doctrine, Security, etc.)
│   └── routes/         # Routes
├── migrations/         # Migrations base de données
├── public/             # Fichiers publics (CSS, images, uploads)
│   ├── css/
│   ├── images/
│   └── uploads/
├── src/
│   ├── Controller/     # Contrôleurs
│   ├── Entity/         # Entités Doctrine
│   ├── Form/           # Formulaires Symfony
│   ├── Repository/    # Repositories
│   ├── Security/       # Sécurité (EmailVerifier)
│   └── Service/        # Services
├── templates/          # Templates Twig
│   ├── base.html.twig
│   ├── produit/
│   ├── panier/
│   ├── commande/
│   ├── kyc/
│   ├── vendeur/
│   ├── security/
│   └── registration/
└── translations/       # Traductions
```

### 3.2 Technologies Utilisées

| Composant | Technologie |
|-----------|-------------|
| Framework | Symfony 6.x |
| Base de données | MySQL/MariaDB (Doctrine ORM) |
| Templating | Twig |
| Authentication | Symfony Security |
| Validation | Symfony Validator |
| Forms | Symfony Form |
| Email | Symfony Mailer |
| Authentification API | JWT (LexikJWTAuthentication) |

### 3.3 Base de Données (Schéma Relationnel)

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│    User     │     │   Admin     │     │  Categorie  │
├─────────────┤     ├─────────────┤     ├─────────────┤
│ id (PK)     │────<│ user_id(FK) │     │ id (PK)     │
│ email       │     │ niveau      │     │ nom         │
│ password    │     │ permissions │     └──────┬──────┘
│ roles (JSON)│     └──────┬──────┘            │
│ isVerified  │            │            ┌─────<┴─────┐
│ isVendeur   │     ┌──────┴──────┐     │            │
│ isKycValidé │     │    Kyc     │     │  Produit   │
│ nomComplet  │     ├─────────────┤     ├─────────────┤
│ telephone   │     │ id (PK)     │     │ id (PK)     │
└──────┬──────┘     │ utilisateur │─┐   │ nom         │
       │            │ typePiece   │  │   │ description │
       │            │ status      │  │   │ prix        │
       │            └─────────────┘  │   │ quantite    │
       │                             │   │ image       │
       │    ┌─────────────┐    ┌────<┤   │ utilisateur │
       │    │ Exploitation│    │     │   │ categorie   │
       ├────< id (PK)     │    │     │   └─────────────┘
       │    │ nom         │    │     │
       │    │ superficie  │    │     │    ┌─────────────┐
       │    │ localisation│    │     └────<│ LigneCmd   │
       │    │ utilisateur │─┐  │          ├─────────────┤
       │    └─────────────┘ │  │          │ id (PK)     │
       │                    │  │          │ commande_id │
       │    ┌─────────────┐ │  │     ┌────< produit_id │
       │    │SuiviCulture │ │  │     │    │ quantite    │
       ├────< id (PK)     │ │  │     │    │ prixUnitaire│
       │    │ exploitation│─┘  │     │    └─────────────┘
       │    │ categorie   │     │     │    ┌─────────────┐
       │    │ nom         │     └────<│    │  Commande   │
       │    │ superficie  │          │    ├─────────────┤
       │    └─────────────┘          │    │ id (PK)     │
       │                             │    │ utilisateur │
       │    ┌─────────────┐         │    │ montantTotal│
       └────<│  Rendement   │         │    │ status      │
            │ id (PK)      │         │    │ dateCommande│
            │ suiviCulture│─┘        │    └──────┬──────┘
            └─────────────┘               │    ┌─────────────┐
                                          │    │   Paiement  │
            ┌─────────────┐               ├────< id (PK)     │
            │ Equipement  │               │    │ utilisateur │
            ├─────────────┤               │    │ commande    │
            │ id (PK)     │               │    │ montant     │
            │ exploitation│─┘             │    │ methode     │
            │ nom         │               │    │ transaction │
            │ type        │               │    └─────────────┘
            └─────────────┘
```

---

## 4. ROUTES ET POINTS D'ACCÈS

### 4.1 Routes Publiques

| Route | Contrôleur | Méthode | Description |
|-------|------------|---------|-------------|
| `/` | HomeController | index | Page d'accueil |
| `/login` | SecurityController | login | Formulaire connexion |
| `/register` | RegistrationController | register | Inscription |
| `/produits` | ProductController | index | Liste produits |
| `/produit/{id}` | ProductController | detail | Détail produit |

### 4.2 Routes Utilisateur (ROLE_USER)

| Route | Contrôleur | Description |
|-------|------------|-------------|
| `/panier` | PanierController | Voir panier |
| `/panier/ajouter/{id}` | PanierController | Ajouter produit |
| `/panier/modifier/{id}` | PanierController | Modifier quantité |
| `/panier/supprimer/{id}` | PanierController | Supprimer produit |
| `/panier/vider` | PanierController | Vider panier |
| `/commande/passer` | CommandeController | Passer commande |
| `/commande/confirmee/{id}` | CommandeController | Confirmation |
| `/commande/mes-commandes` | CommandeController | Mes commandes |
| `/commande/details/{id}` | CommandeController | Détails commande |
| `/kyc/submit` | KycController | Soumettre KYC |
| `/kyc/status` | KycController | Statut KYC |

### 4.3 Routes Vendeur (ROLE_VENDEUR)

| Route | Contrôleur | Description |
|-------|------------|-------------|
| `/vendeur/dashboard` | VendeurController | Dashboard vendeur |
| `/vendeur/produits` | VendeurController | Mes produits |
| `/vendeur/produit/new` | VendeurController | Nouveau produit |
| `/vendeur/produit/{id}/edit` | VendeurController | Modifier produit |
| `/vendeur/commandes` | VendeurController | Commandes reçus |

### 4.4 Routes Administrateur (ROLE_ADMIN)

| Route | Contrôleur | Description |
|-------|------------|-------------|
| `/kyc/admin/list` | KycController | Liste KYC |
| `/kyc/admin/validate/{id}` | KycController | Valider KYC |
| `/kyc/admin/detail/{id}` | KycController | Détails KYC |

---

## 5. INTERFACES UTILISATEUR (TEMPLATES TWIG)

### 5.1 Templates Principaux

| Template | Description |
|----------|-------------|
| `base.html.twig` | Template de base (header, footer) |
| `home/index.html.twig` | Page d'accueil |
| `security/login.html.twig` | Page de connexion |
| `registration/register.html.twig` | Page d'inscription |
| `product/index.html.twig` | Catalogue produits |
| `product/detail.html.twig` | Détail produit |
| `panier/index.html.twig` | Panier |
| `commande/confirmee.html.twig` | Confirmation commande |
| `commande/mes_commandes.html.twig` | Liste commandes |
| `commande/details.html.twig` | Détails commande |

### 5.2 Templates Vendeur

| Template | Description |
|----------|-------------|
| `vendeur/dashboard.html.twig` | Dashboard |
| `vendeur/produits.html.twig` | Gestion produits |
| `vendeur/produit_form.html.twig` | Formulaire produit |
| `vendeur/commandes.html.twig` | Commandes reçues |

### 5.3 Templates KYC

| Template | Description |
|----------|-------------|
| `kyc/submit.html.twig` | Soumission KYC |
| `kyc/status.html.twig` | Statut KYC |
| `kyc/admin_list.html.twig` | Liste admin KYC |

---

## 6. SÉCURITÉ

### 6.1 Mesures Implémentées

1. **Hachage des mots de passe** : Bcrypt (cost: 12)
2. **Vérification email** : Code de confirmation
3. **OTP (One-Time Password)** : Code à 6 chiffres avec expiration
4. **Validation KYC** : Vérification d'identité obligatoire pour vendre
5. **Rôles et permissions** : Système de contrôle d'accès (ACL)
6. **Protection CSRF** : Tokens CSRF sur tous les formulaires
7. **Validation des entrées** : Constraints Symfony Validator

### 6.2 Firewall Configuration

```yaml
access_control:
  - { path: ^/login, roles: PUBLIC_ACCESS }
  - { path: ^/register, roles: PUBLIC_ACCESS }
  - { path: ^/produits, roles: PUBLIC_ACCESS }
  - { path: ^/panier, roles: ROLE_USER }
  - { path: ^/commande, roles: ROLE_USER }
  - { path: ^/vendeur, roles: ROLE_USER }
  - { path: ^/kyc/submit, roles: ROLE_USER }
  - { path: ^/kyc/status, roles: ROLE_USER }
  - { path: ^/kyc/admin, roles: ROLE_ADMIN }
```

---

## 7. FONCTIONNALITÉS À DÉVELOPPER

### 7.1 Priorité Haute
- [ ] Tableau de bord administrateur complet
- [ ] Système de messagerie interne
- [ ] Module de statistiques (ventes, utilisateurs)
- [ ] Système de gestion des exploitations (CRUD complet)
- [ ] Suivi cultural avancé avec alertes

### 7.2 Priorité Moyenne
- [ ] Module de conseil avec gestion complète
- [ ] Système de notation/avis produits
- [ ] Gestion des équipements
- [ ] Alertes automatique stocks
- [ ] Export données (Excel/PDF)

### 7.3 Priorité Basse
- [ ] Application mobile (API REST)
- [ ] Intégration paiement mobile (Orange Money, MTN Mobile Money)
- [ ] Chat en direct
- [ ] Système de fidélisation
- [ ] Programme de points

---

## 8. GLOSSAIRE

| Terme | Définition |
|-------|------------|
| **Exploitation** | Ferme ou terrain agricole géré par un utilisateur |
| **Suivi Culture** | Monitoring des cultures (semis, croissance, récolte) |
| **KYC** | Know Your Customer - Vérification d'identité |
| **Rendement** | Quantité produite par une culture |
| **Ligne Commande** | Article individuel dans une commande |
| **OTP** | One-Time Password - Code temporaire |

---

## 9. ANNEXES

### 9.1 Dépendances Composer

```json
{
    "symfony/framework-bundle": "^6.0",
    "symfony/security-bundle": "^6.0",
    "symfony/form": "^6.0",
    "symfony/validator": "^6.0",
    "symfony/mailer": "^6.0",
    "doctrine/orm": "^2.12",
    "doctrine/doctrine-bundle": "^2.7",
    "lexik/jwt-authentication-bundle": "^2.16"
}
```

### 9.2 Configuration Requise

- PHP 8.1+
- MySQL 8.0+ / MariaDB 10.5+
- Apache/Nginx
- Composer
- Node.js (pour assets)

---

**Document généré automatiquement à partir du code source du projet KilysAgri**

*Fin du cahier des charges*

