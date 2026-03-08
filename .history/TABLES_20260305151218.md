# Tables de la Base de Données - KilysAgri

## Contexte du Projet
Plateforme Web pour l'Accès aux Marchés et l'Amélioration de la Productivité Agricole

## Acteurs
- **Visiteur** : Peut consulter les produits, catégories, recherches
- **Acheteur** : Utilisateur connecté qui achète des produits
- **Producteur** : Vendeur validé après KYC
- **Administrateur** : Gestion de la plateforme

---

## Liste des Tables

### 1. **user** (Utilisateur)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire, auto-incrément |
| email | VARCHAR(180) | NON | Email unique |
| password | VARCHAR(255) | NON | Mot de passe hashé |
| nom | VARCHAR(100) | NON | Nom |
| prenom | VARCHAR(100) | NON | Prénom |
| nom_complet | VARCHAR(255) | OUI | Nom complet |
| telephone | VARCHAR(20) | OUI | Numéro de téléphone |
| roles | JSON | NON | Rôles [ROLE_USER, ROLE_PRODUCTEUR, ROLE_ADMIN] |
| is_verified | BOOLEAN | NON | Compte vérifié (email) |
| is_vendeur | BOOLEAN | NON | Peut vendre (après KYC) |
| is_kyc_validated | BOOLEAN | NON | KYC validé par admin |
| confirmation_code | VARCHAR(6) | OUI | Code OTP |
| code_expires_at | DATETIME | OUI | Expiration du code OTP |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

**Rôle :** Gère tous les utilisateurs (acheteurs, producteurs, admins)

---

### 2. **kyc** (Vérification d'identité - Know Your Customer)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_utilisateur | INT | NON | FK vers user |
| type_piece | VARCHAR(50) | NON | cni, passeport, permis_conduire |
| numero_piece | VARCHAR(100) | NON | Numéro de la pièce d'identité |
| photo_piece_recto | VARCHAR(255) | NON | Photo recto de la pièce |
| photo_piece_verso | VARCHAR(255) | NON | Photo verso de la pièce |
| photo_selfie | VARCHAR(255) | NON | Photo selfie avec la pièce |
| adresse_complete | TEXT | OUI | Adresse complète |
| region | VARCHAR(100) | OUI | Région |
| superficie_exploitee | DECIMAL(10,2) | OUI | Superficie exploitée (hectares) |
| type_agriculture | VARCHAR(100) | OUI | Type d'agriculture pratiquée |
| experience | INT | OUI | Années d'expérience |
| statut | VARCHAR(20) | NON | en_attente, valide, rejete |
| created_at | DATETIME | NON | Date de soumission |
| updated_at | DATETIME | OUI | Date de mise à jour |

**Rôle :** Vérification d'identité pour devenir producteur

---

### 3. **categorie** (Catégorie de produits)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| nom | VARCHAR(255) | NON | Nom (céréales, fruits, légumes, tubercules, élevage, etc.) |
| description | TEXT | OUI | Description de la catégorie |
| image | VARCHAR(255) | OUI | Image de la catégorie |
| ordre | INT | OUI | Ordre d'affichage |
| active | BOOLEAN | NON | Catégorie active ou non |
| created_at | DATETIME | NON | Date de création |

**Rôle :** Organisation des produits par catégorie

---

### 4. **produit** (Produit agricole)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_utilisateur | INT | NON | FK vers user (producteur) |
| categorie_id | INT | NON | FK vers categorie |
| nom | VARCHAR(255) | NON | Nom du produit |
| description | TEXT | OUI | Description détaillée |
| prix | DECIMAL(10,2) | NON | Prix unitaire |
| quantite | INT | NON | Quantité en stock |
| unite | VARCHAR(20) | NON | kg, tonne, piece, lot |
| image | VARCHAR(255) | OUI | Chemin de l'image principale |
| images | JSON | OUI | Images supplémentaires |
| est_actif | BOOLEAN | NON | Produit visible ou non |
| est_vedette | BOOLEAN | NON | Produit en vedette |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

**Rôle :** Produits publiés par les producteurs

---

### 5. **commande** (Commande client)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_utilisateur | INT | NON | FK vers user (acheteur) |
| montant_total | DECIMAL(10,2) | NON | Montant total |
| frais_livraison | DECIMAL(10,2) | OUI | Frais de livraison |
| statut | VARCHAR(20) | NON | en_attente, payee, en_cours, livree, annulee |
| adresse_livraison | TEXT | OUI | Adresse de livraison |
| notes | TEXT | OUI | Notes de la commande |
| date_commande | DATETIME | NON | Date de la commande |
| date_livraison | DATETIME | OUI | Date de livraison prévue |

**Rôle :** Gestion des commandes des acheteurs

---

### 6. **ligne_commande** (Ligne de commande)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| commande_id | INT | NON | FK vers commande |
| produit_id | INT | NON | FK vers produit |
| quantite | INT | NON | Quantité commandée |
| prix_unitaire | DECIMAL(10,2) | NON | Prix au moment de la commande |
| sous_total | DECIMAL(10,2) | NON | Sous-total (quantité × prix) |

**Rôle :** Détail des produits dans une commande

---

### 7. **paiement** (Paiement - Intégration FedaPay)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| commande_id | INT | NON | FK vers commande |
| montant | DECIMAL(10,2) | NON | Montant payé |
| reference | VARCHAR(100) | NON | Référence FedaPay |
| methode | VARCHAR(50) | OUI | moov_money, orange_money, waved |
| statut | VARCHAR(20) | NON | en_attente, valide, echoue |
| date_paiement | DATETIME | OUI | Date du paiement |
| transaction_id | VARCHAR(100) | OUI | ID transaction FedaPay |

**Rôle :** Traçabilité des paiements via FedaPay

---

### 8. **exploitation** (Exploitation agricole / Parcelle)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_utilisateur | INT | NON | FK vers user (producteur) |
| nom | VARCHAR(255) | NON | Nom de la parcelle |
| superficie | DECIMAL(10,2) | NON | Superficie (hectares) |
| localisation | VARCHAR(255) | OUI | Localisation |
| type_sol | VARCHAR(100) | OUI | Type de sol |
| source_eau | VARCHAR(100) | OUI | Source d'eau |
| latitude | DECIMAL(10,8) | OUI | Latitude GPS |
| longitude | DECIMAL(11,8) | OUI | Longitude GPS |
| statut | VARCHAR(50) | NON | active, inactive |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

**Rôle :** Gestion des parcelles par les producteurs

---

### 9. **suivi_culture** (Suivi des cultures)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| exploitation_id | INT | NON | FK vers exploitation |
| type_culture | VARCHAR(100) | NON | Type de culture |
| date_plantation | DATE | NON | Date de plantation |
| date_recolte_prevue | DATE | OUI | Date de récolte prévue |
| date_recolte_effective | DATE | OUI | Date de récolte effective |
| superficie | FLOAT | NON | Superficie cultivée |
| observations | TEXT | OUI | Observations |
| statut | VARCHAR(50) | NON | en_cours, recolte, terminee |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de modification |

**Rôle :** Suivi des cultures par parcelle

---

### 10. **rendement** (Rendement agricole)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| exploitation_id | INT | NON | FK vers exploitation |
| suivi_culture_id | INT | OUI | FK vers suivi_culture |
| type_culture | VARCHAR(100) | NON | Type de culture |
| quantite_produite | DECIMAL(10,2) | NON | Quantité produite |
| unite | VARCHAR(20) | NON | kg, tonne |
| rendement_hectare | DECIMAL(10,2) | OUI | Rendement à l'hectare |
| date_recolte | DATE | OUI | Date de récolte |
| created_at | DATETIME | NON | Date de création |

**Rôle :** Statistiques des rendements par exploitation

---

### 11. **notification** (Notifications)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_utilisateur | INT | NON | FK vers user |
| titre | VARCHAR(255) | NON | Titre de la notification |
| message | TEXT | NON | Message |
| type | VARCHAR(50) | NON | commande, paiement, kyc, systeme |
| est_lu | BOOLEAN | NON | Lu ou non |
| lien | VARCHAR(255) | OUI | Lien vers la page concernée |
| created_at | DATETIME | NON | Date de création |

**Rôle :** Notifications aux utilisateurs (commande, paiement, KYC)

---

### 12. **conversation** (Messagerie interne)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_expediteur | INT | NON | FK vers user (expéditeur) |
| id_destinataire | INT | NON | FK vers user (destinataire) |
| id_produit | INT | OUI | FK vers produit (conversation liée) |
| dernier_message | TEXT | OUI | Aperçu du dernier message |
| created_at | DATETIME | NON | Date de création |
| updated_at | DATETIME | OUI | Date de mise à jour |

**Rôle :** Conversations entre acheteurs et producteurs

---

### 13. **message** (Messages)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| conversation_id | INT | NON | FK vers conversation |
| id_expediteur | INT | NON | FK vers user |
| contenu | TEXT | NON | Contenu du message |
| est_lu | BOOLEAN | NON | Lu ou non |
| created_at | DATETIME | NON | Date d'envoi |

**Rôle :** Messages échangés dans les conversations

---

### 14. **conseil** (Conseils agricoles)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_auteur | INT | NON | FK vers user |
| titre | VARCHAR(255) | NON | Titre du conseil |
| contenu | TEXT | NON | Contenu |
| categorie | VARCHAR(100) | OUI | Culture, Elevage, Sol, etc. |
| est_publie | BOOLEAN | NON | Publié ou non |
| created_at | DATETIME | NON | Date de création |

**Rôle :** Conseils agricoles (optionnel, peut être généré par IA)

---

### 15. **signalement** (Signalements)
| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| id | INT | NON | Clé primaire |
| id_signaleur | INT | NON | FK vers user (qui signale) |
| id_signale | INT | NON | FK vers user (signalé) |
| id_produit | INT | OUI | FK vers produit (signalé) |
| id_commande | INT | OUI | FK vers commande |
| motif | VARCHAR(255) | NON | Motif du signalement |
| description | TEXT | OUI | Description |
| statut | VARCHAR(20) | NON | en_attente, traite, rejete |
| created_at | DATETIME | NON | Date du signalement |

**Rôle :** Signalements de comportements Frauduleux

---

## Résumé des Cardinalités

| Table 1 | Table 2 | Cardinalité | Description |
|---------|---------|-------------|-------------|
| user | kyc | 1 : 0..* | 1 utilisateur soumet 0 à plusieurs KYC |
| user | produit | 1 : 0..* | 1 producteur publie 0 à plusieurs produits |
| user | commande | 1 : 0..* | 1 acheteur passe 0 à plusieurs commandes |
| user | exploitation | 1 : 0..* | 1 agriculteur possède 0 à plusieurs parcelles |
| user | notification | 1 : 0..* | 1 utilisateur reçoit 0 à plusieurs notifications |
| user | conversation | 1 : 0..* | 1 utilisateur peut avoir plusieurs conversations |
| categorie | produit | 1 : 0..* | 1 catégorie contient 0 à plusieurs produits |
| commande | ligne_commande | 1 : 0..* | 1 commande contient 0 à plusieurs lignes |
| commande | paiement | 1 : 0..1 | 1 commande a 0 ou 1 paiement |
| ligne_commande | produit | N : 1 | Plusieurs lignes concernent 1 produit |
| exploitation | suivi_culture | 1 : 0..* | 1 parcelle a 0 à plusieurs suivis |
| exploitation | rendement | 1 : 0..* | 1 parcelle a 0 à plusieurs rendements |
| conversation | message | 1 : 0..* | 1 conversation contient 0 à plusieurs messages |

---

## Statuts des Tables

### kyc.statut
- `en_attente` : En attente de validation
- `valide` : Validé par l'administrateur
- `rejete` : Rejeté par l'administrateur

### commande.statut
- `en_attente` : Commande créée, en attente de paiement
- `payee` : Paiement validé via FedaPay
- `en_cours` : Commande en cours de traitement
- `livree` : Commande livrée
- `annulee` : Commande annulée

### paiement.statut
- `en_attente` : Paiement en cours
- `valide` : Paiement validé
- `echoue` : Paiement échoué

### exploitation.statut
- `active` : Exploitation active
- `inactive` : Exploitation inactive

### suivi_culture.statut
- `en_cours` : Culture en cours
- `recolte` : Prêt pour récolte
- `terminee` : Récolte terminée

### signalement.statut
- `en_attente` : En attente de traitement
- `traite` : Signalement traité
- `rejete` : Signalement rejeté
