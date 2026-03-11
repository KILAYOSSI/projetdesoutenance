# Tables de la Base de Données - KilysAgri

## Contexte du Projet
Plateforme Web pour l'Accès aux Marchés et l'Amélioration de la Productivité Agricole

## Acteurs
- **Visiteur** : Peut consulter les produits, catégories, recherches
- **Acheteur** : Utilisateur connecté qui achète des produits
- **Producteur** : Vendeur validé après KYC
- **Administrateur** : Gestion de la plateforme

---

## Liste des Tables (Triée par ordre logique)

---

### 1. **user** (Utilisateur)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| email | VARCHAR(180) | NON | Email unique |
| password | VARCHAR(255) | NON | Mot de passe hashé |
| roles | JSON | NON | Rôles [ROLE_USER, ROLE_PRODUCTEUR, ROLE_ADMIN] |
| is_verified | BOOLEAN | NON | Compte vérifié (email), défaut: false |
| confirmation_code | VARCHAR(6) | OUI | Code OTP |
| code_expires_at | DATETIME | OUI | Expiration du code OTP |
| is_vendeur | BOOLEAN | NON | Peut vendre (après KYC), défaut: false |
| is_kyc_validated | BOOLEAN | NON | KYC validé par admin, défaut: false |
| nom_complet | VARCHAR(255) | OUI | Nom complet |
| telephone | VARCHAR(20) | OUI | Numéro de téléphone |
| created_at | DATETIME | NON | Date de création |

**Relations :**
- OneToMany: produits, kycs, commandes, notifications, exploitations, conseils
- OneToOne: admin

---

### 2. **admin** (Administrateur)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| user_id | INT | NON | FK vers user |
| niveau | VARCHAR(50) | NON | super_admin, admin, moderateur |
| permissions | JSON | OUI | Permissions spécifiques |
| est_actif | BOOLEAN | NON | Admin actif, défaut: true |
| telephone | VARCHAR(255) | OUI | Téléphone |
| adresse | VARCHAR(255) | OUI | Adresse |
| date_nomination | DATETIME | NON | Date de nomination |
| derniere_connexion | DATETIME | OUI | Dernière connexion |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de mise à jour |

---

### 3. **kyc** (Vérification d'identité - Know Your Customer)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| id_utilisateur | INT | NON | FK vers user |
| type_piece | VARCHAR(50) | NON | cni, passeport, permis_conduire |
| numero_piece | VARCHAR(100) | NON | Numéro de la pièce d'identité |
| photo_piece_recto | VARCHAR(255) | OUI | Photo recto de la pièce |
| photo_piece_verso | VARCHAR(255) | OUI | Photo verso de la pièce |
| photo_selfie | VARCHAR(255) | OUI | Photo selfie avec la pièce |
| status | VARCHAR(20) | NON | en_attente, validé, rejeté |
| created_at | DATETIME | NON | Date de création |

---

### 4. **categorie** (Catégorie de produits)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| nom | VARCHAR(255) | NON | Nom (céréales, fruits, légumes, tubercules, élevage, etc.) |

**Relations :**
- OneToMany: produits

---

### 5. **produit** (Produit agricole)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| id_utilisateur | INT | NON | FK vers user (producteur) |
| categorie_id | INT | NON | FK vers categorie |
| nom | VARCHAR(255) | NON | Nom du produit |
| description | TEXT | OUI | Description détaillée |
| prix | DECIMAL(10,2) | NON | Prix unitaire |
| quantite | INT | NON | Quantité en stock |
| image | VARCHAR(255) | OUI | Chemin de l'image principale |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

---

### 6. **commande** (Commande client)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| id_utilisateur | INT | NON | FK vers user (acheteur) |
| montant_total | DECIMAL(10,2) | NON | Montant total |
| status | VARCHAR(20) | NON | en_attente, confirmé, en_cours, livré, annulé |
| date_commande | DATETIME | NON | Date de la commande |

**Relations :**
- OneToMany: ligneCommandes

---

### 7. **ligne_commande** (Ligne de commande)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| commande_id | INT | NON | FK vers commande |
| produit_id | INT | NON | FK vers produit |
| quantite | INT | NON | Quantité commandée |
| prix_unitaire | DECIMAL(10,2) | NON | Prix au moment de la commande |

---

### 8. **paiement** (Paiement - Intégration FedaPay)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| id_utilisateur | INT | NON | FK vers user |
| commande_id | INT | NON | FK vers commande |
| montant | DECIMAL(10,2) | NON | Montant payé |
| statut | VARCHAR(50) | NON | Statut du paiement |
| methode | VARCHAR(50) | NON | moov_money, orange_money, waved |
| reference | VARCHAR(255) | OUI | Référence FedaPay |
| transaction_id | VARCHAR(255) | OUI | ID transaction FedaPay |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de mise à jour |
| date_paiement | DATETIME | OUI | Date du paiement |

---

### 9. **exploitation** (Exploitation agricole / Parcelle)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| id_utilisateur | INT | NON | FK vers user (producteur) |
| nom | VARCHAR(255) | NON | Nom de la parcelle |
| superficie | DECIMAL(10,2) | OUI | Superficie (hectares) |
| localisation | VARCHAR(255) | OUI | Localisation |
| type_sol | VARCHAR(100) | OUI | Type de sol |
| source_eau | VARCHAR(100) | OUI | Source d'eau |
| statut | VARCHAR(50) | OUI | Statut de l'exploitation |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

**Relations :**
- OneToMany: suiviCultures, equipements, stocks

---

### 10. **suivi_culture** (Suivi des cultures)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| exploitation_id | INT | NON | FK vers exploitation |
| categorie_id | INT | OUI | FK vers categorie |
| nom | VARCHAR(255) | NON | Nom de la culture |
| variete | VARCHAR(100) | OUI | Variété |
| superficie | DECIMAL(10,2) | OUI | Superficie cultivée |
| date_semis | DATE | OUI | Date de semis |
| date_recolte_prevue | DATE | OUI | Date de récolte prévue |
| date_recolte_reelle | DATE | OUI | Date de récolte réelle |
| statut | VARCHAR(50) | OUI | en_cours, recolte, terminee |
| observations | TEXT | OUI | Observations |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

**Relations :**
- OneToMany: rendements

---

### 11. **rendement** (Rendement agricole)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| suivi_culture_id | INT | OUI | FK vers suivi_culture |
| quantite | DECIMAL(10,2) | OUI | Quantité produite |
| unite | VARCHAR(50) | OUI | kg, tonne |
| date_recolte | DATE | OUI | Date de récolte |
| observations | TEXT | OUI | Observations |
| created_at | DATETIME | NON | Date de création |

---

### 12. **notification** (Notifications)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| id_utilisateur | INT | NON | FK vers user |
| titre | VARCHAR(255) | NON | Titre de la notification |
| message | TEXT | NON | Message |
| type | VARCHAR(50) | NON | Type (commande, paiement, kyc, systeme) |
| lu | BOOLEAN | NON | Lu ou non, défaut: false |
| created_at | DATETIME | NON | Date de création |
| read_at | DATETIME | OUI | Date de lecture |

---

### 13. **conversation** (Messagerie interne)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| participant1_id | INT | NON | FK vers user (participant 1) |
| participant2_id | INT | NON | FK vers user (participant 2) |
| created_at | DATETIME | NON | Date de création |
| last_message_at | DATETIME | OUI | Dernier message |

**Relations :**
- OneToMany: messages

---

### 14. **message** (Messages)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| conversation_id | INT | NON | FK vers conversation |
| sender_id | INT | NON | FK vers user (expéditeur) |
| content | TEXT | NON | Contenu du message |
| is_read | BOOLEAN | NON | Lu ou non, défaut: false |
| is_deleted | BOOLEAN | NON | Supprimé, défaut: false |
| deleted_for_everyone | BOOLEAN | OUI | Supprimé pour tous |
| is_edited | BOOLEAN | NON | Édité, défaut: false |
| edited_at | DATETIME | OUI | Date d'édition |
| created_at | DATETIME | NON | Date d'envoi |

---

### 15. **conseil** (Conseils agricoles)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| auteur_id | INT | NON | FK vers user |
| titre | VARCHAR(255) | NON | Titre du conseil |
| contenu | TEXT | NON | Contenu |
| type | VARCHAR(50) | NON | Type de conseil |
| categorie | VARCHAR(100) | OUI | Culture, Elevage, Sol, etc. |
| image | VARCHAR(255) | OUI | Image |
| est_publie | BOOLEAN | NON | Publié ou non, défaut: false |
| date_publication | DATETIME | NON | Date de publication |
| vues | INT | OUI | Nombre de vues |

---

### 16. **equipement** (Équipements agricoles)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| exploitation_id | INT | NON | FK vers exploitation |
| nom | VARCHAR(255) | NON | Nom de l'équipement |
| type | VARCHAR(100) | NON | Type (tracteur, pompe, etc.) |
| etat | VARCHAR(50) | OUI | État (bon, moyen, mauvais) |
| date_achat | DATE | OUI | Date d'achat |
| observations | TEXT | OUI | Observations |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

---

### 17. **stock** (Gestion des stocks)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| exploitation_id | INT | NON | FK vers exploitation |
| nom | VARCHAR(255) | NON | Nom du stock |
| type | VARCHAR(100) | NON | Type (semence, engrais, produit, etc.) |
| quantite | DECIMAL(10,2) | NON | Quantité |
| unite | VARCHAR(50) | NON | Unité (kg, tonne, sac, litre) |
| seuil_alerte | DECIMAL(10,2) | OUI | Seuil d'alerte |
| date_expiration | DATE | OUI | Date d'expiration |
| observations | TEXT | OUI | Observations |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

---

## Résumé des Cardinalités

| Table 1 | Table 2 | Cardinalité | Description |
|---------|---------|-------------|-------------|
| user | admin | 1 : 0..1 | 1 utilisateur est au plus 1 admin |
| user | kyc | 1 : 0..* | 1 utilisateur soumet 0 à plusieurs KYC |
| user | produit | 1 : 0..* | 1 producteur publie 0 à plusieurs produits |
| user | commande | 1 : 0..* | 1 acheteur passe 0 à plusieurs commandes |
| user | exploitation | 1 : 0..* | 1 agriculteur possède 0 à plusieurs parcelles |
| user | notification | 1 : 0..* | 1 utilisateur reçoit 0 à plusieurs notifications |
| user | conversation | 1 : 0..* | 1 utilisateur peut avoir plusieurs conversations |
| user | conseil | 1 : 0..* | 1 utilisateur peut écrire plusieurs conseils |
| categorie | produit | 1 : 0..* | 1 catégorie contient 0 à plusieurs produits |
| commande | ligne_commande | 1 : 0..* | 1 commande contient 0 à plusieurs lignes |
| commande | paiement | 1 : 0..1 | 1 commande a 0 ou 1 paiement |
| ligne_commande | produit | N : 1 | Plusieurs lignes concernent 1 produit |
| exploitation | suivi_culture | 1 : 0..* | 1 parcelle a 0 à plusieurs suivis |
| exploitation | equipement | 1 : 0..* | 1 parcelle a 0 à plusieurs équipements |
| exploitation | stock | 1 : 0..* | 1 parcelle a 0 à plusieurs stocks |
| suivi_culture | rendement | 1 : 0..* | 1 culture peut avoir plusieurs rendements |
| conversation | message | 1 : 0..* | 1 conversation contient 0 à plusieurs messages |

---

## Statuts des Tables

### kyc.status
- `en_attente` : En attente de validation
- `validé` : Validé par l'administrateur
- `rejeté` : Rejeté par l'administrateur

### commande.status
- `en_attente` : Commande créée, en attente de paiement
- `confirmé` : Paiement validé via FedaPay
- `en_cours` : Commande en cours de traitement
- `livré` : Commande livrée
- `annulé` : Commande annulée

### paiement.statut
- Différentes valeurs selon le traitement FedaPay

### exploitation.statut
- Valeurs possibles : active, inactive

### suivi_culture.statut
- `en_cours` : Culture en cours
- `recolte` : Prêt pour récolte
- `terminee` : Récolte terminée

### equipement.etat
- `bon` : En bon état (défaut)
- `moyen` : État moyen
- `mauvais` : Mauvais état

### admin.niveau
- `super_admin` : Super administrateur
- `admin` : Administrateur
- `moderateur` : Modérateur

### notification.type
- `commande` : Notification de commande
- `paiement` : Notification de paiement
- `kyc` : Notification KYC
- `systeme` : Notification système
