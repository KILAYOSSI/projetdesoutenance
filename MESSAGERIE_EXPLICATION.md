# SYSTÈME DE MESSAGERIE KILYSAGRI - Explication Complète

## 1. LES DEUX TABLES (Base de données)

### Table conversation
| Colonne | Type | Description |
|---------|------|-------------|
| id | INT | Clé primaire |
| participant1_id | INT | FK vers user (1er participant) |
| participant2_id | INT | FK vers user (2ème participant) |
| created_at | DATETIME | Date de création |
| last_message_at | DATETIME | Date du dernier message |

### Table message
| Colonne | Type | Description |
|---------|------|-------------|
| id | INT | Clé primaire |
| conversation_id | INT | FK vers conversation |
| sender_id | INT | FK vers user (expéditeur) |
| content | TEXT | Contenu du message |
| is_read | BOOLEAN | Lu ou non |
| is_deleted | BOOLEAN | Supprimé |
| deleted_for_everyone | BOOLEAN | Supprimé pour tous |
| is_edited | BOOLEAN | Modifié |
| edited_at | DATETIME | Date de modification |
| created_at | DATETIME | Date d'envoi |

---

## 2. LES ROUTES (MessagerieController.php)

| Route | Méthode | Description |
|-------|---------|-------------|
| /messagerie | GET | Liste toutes les conversations |
| /messagerie/{id} | GET | Affiche une conversation |
| /messagerie/start/{userId} | GET | Démarre une nouvelle conversation |
| /messagerie/send/{conversationId} | POST | Envoie un message |
| /messagerie/api/conversations | GET | API - Liste conversations JSON |
| /messagerie/api/messages/{id} | GET | API - Liste messages JSON |
| /messagerie/message/{id}/edit | POST | Modifie un message |
| /messagerie/message/{id}/delete | POST | Supprime un message |

---

## 3. LES FONCTIONNALITÉS

### a) Créer une conversation
- Vérifie si une conversation existe déjà entre les 2 utilisateurs
- Si non, crée une nouvelle conversation avec participant1 et participant2

### b) Envoyer un message
- Vérifie que l'utilisateur est connecté
- Vérifie qu'il fait partie de la conversation
- BLOQUE les numéros de téléphone et emails (sécurité anti-arnaque)
- Crée le message avec expéditeur et date

### c) Marquer comme lu
- Quand on ouvre une conversation, tous les messages de l'autre utilisateur passent à is_read = true

### d) Modifier un message
- Seul l'expéditeur peut modifier
- Marque is_edited = true avec la date

### e) Supprimer un message
- Suppression molle (is_deleted = true) : affiche "Ce message a été supprimé"
- Suppression pour tous : vraiment supprimé de la base

---

## 4. LES REPOSITORIES

### ConversationRepository
- findByUser(user) → Liste les conversations d'un utilisateur
- findConversationBetweenUsers(user1, user2) → Trouve conversation existante

### MessageRepository
- findByConversation(conversation) → Liste les messages
- markAllAsRead(conversation, userId) → Marque comme lus
- countUnreadForUser(userId) → Compte messages non lus

---

## 5. SÉCURITÉ

- Protection anti-arnaque : Les numéros et emails sont bloqués dans les messages
- Vérification : Chaque action vérifie que l'utilisateur appartient à la conversation
- Sanitization : Le contenu est échappé avec htmlspecialchars()

---

## Schéma de fonctionnement

```
Utilisateur A                    Conversation                   Utilisateur B
    |                                |                              |
    |-- startConversation() ------>|                              |
    |<------ redirect(id) ---------|                              |
    |                                |                              |
    |-- sendMessage() ------------->|                              |
    |                                |---- sendMessage() -------->|
    |                                |                              |
    |<----- messages (JSON) --------|                              |
```

