# Diagramme de Classes - KilysAgri

```mermaid
classDiagram
    class User {
        +int id
        +string email
        +string password
        +string nomComplet
        +string telephone
        +boolean isVerified
        +boolean isVendeur
        +boolean isKycValidated
        +json roles
        +datetime createdAt
    }

    class Kyc {
        +int id
        +string typePiece
        +string numeroPiece
        +string photoPieceRecto
        +string photoPieceVerso
        +string photoSelfie
        +string adresseComplete
        +string region
        +float superficieExploitee
        +string typeAgriculture
        +int experience
        +string status
        +datetime createdAt
    }

    class Produit {
        +int id
        +string nom
        +string description
        +decimal prix
        +int quantite
        +string image
        +datetime createdAt
        +datetime updatedAt
    }

    class Categorie {
        +int id
        +string nom
        +string description
        +string image
    }

    class Commande {
        +int id
        +decimal montantTotal
        +string status
        +datetime dateCommande
        +datetime updatedAt
    }

    class LigneCommande {
        +int id
        +int quantite
        +decimal prixUnitaire
    }

    class Paiement {
        +int id
        +decimal montant
        +string reference
        +string methode
        +string status
        +datetime datePaiement
    }

    class Exploitation {
        +int id
        +string nom
        +decimal superficie
        +string localisation
        +string typeSol
        +string sourceEau
        +string statut
        +datetime createdAt
    }

    class SuiviCulture {
        +int id
        +string typeCulture
        +datetime datePlantation
        +datetime dateRecolteEstimee
        +float superficie
        +string observations
        +datetime createdAt
    }

    class Rendement {
        +int id
        +string typeCulture
        +decimal quantite
        +decimal rendementEstime
        +datetime dateRecolte
    }

    class Equipement {
        +int id
        +string nom
        +string type
        +int quantite
    }

    class Stock {
        +int id
        +int quantite
        +datetime dateMisAJour
    }

    class Notification {
        +int id
        +string titre
        +string message
        +boolean isRead
        +datetime createdAt
    }

    class Conversation {
        +int id
        +datetime createdAt
        +datetime updatedAt
    }

    class Message {
        +int id
        +string contenu
        +boolean isRead
        +datetime createdAt
    }

    class Conseil {
        +int id
        +string titre
        +string contenu
        +datetime createdAt
    }

    class Admin {
        +int id
    }

    class OtpCode {
        +int id
        +string code
        +datetime expiresAt
    }

    %% Relations
    User "1" -- "0..*" Kyc : soumet
    User "1" -- "0..*" Produit : publie
    User "1" -- "0..*" Commande : passe
    User "1" -- "0..*" Exploitation : possede
    User "1" -- "0..*" Notification : recoit
    User "1" -- "0..*" Conversation : initie
    User "1" -- "0..*" Conseil : redige
    User "0..1" -- "1" Admin : est_un

    Produit "1" -- "0..*" LigneCommande : contient
    Produit "1" -- "0..*" Stock : a_en_stock
    Produit "many" -- "1" Categorie : appartient_a
    Produit "many" -- "1" User : utilisateur

    Categorie "1" -- "0..*" Produit : contient

    Commande "1" -- "0..*" LigneCommande : composee_de
    Commande "1" -- "0..1" Paiement : possede
    Commande "many" -- "1" User : utilisateur

    LigneCommande "many" -- "1" Produit : concerne
    LigneCommande "many" -- "1" Commande : appartient_a

    Paiement "many" -- "1" Commande : paiement_de

    Exploitation "1" -- "0..*" SuiviCulture : suivi
    Exploitation "1" -- "0..*" Rendement : rendements
    Exploitation "1" -- "0..*" Equipement : equipements
    Exploitation "1" -- "0..*" Stock : stocks
    Exploitation "many" -- "1" User : utilisateur

    SuiviCulture "many" -- "1" Exploitation : culture_de
    Rendement "many" -- "1" Exploitation : exploit

    Conversation "1" -- "0..*" Message : messages
    Conversation "1" -- "1" User : utilisateur1
    Conversation "1" -- "1" User : utilisateur2

    Message "many" -- "1" Conversation : conversation
    Message "many" -- "1" User : auteur
```

## Légende des Statuts

### Statut KYC
- `en_attente` - En cours de validation
- `valide` - Validated
- `rejete` - Rejected

### Statut Commande
- `en_attente` - En attente de paiement
- `payee` - Payée
- `en_cours` - En cours de traitement
- `livree` - Livrée
- `annulee` - Annulée

### Statut Paiement
- `en_attente` - En attente
- `valide` - Validated
- `echoue` - Failed

## Rôles Utilisateurs

| Rôle | Description |
|-------|-------------|
| VISITEUR | Peut seulement parcourir les produits |
| ACHETEUR | Utilisateur connecté, peut acheter |
| PRODUCTEUR | Acheteur + KYC validé, peut vendre |
| ADMIN | Gestion de la plateforme |
