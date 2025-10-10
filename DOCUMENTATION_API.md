# Documentation de l'API DiscoverBF

## Table des matières
1. [Authentification](#authentification)
   - [Inscription](#inscription)
   - [Connexion](#connexion)
2. [Restaurants (public)](#restaurants-public)
   - [Lister les restaurants](#lister-les-restaurants)
   - [Afficher un restaurant](#afficher-un-restaurant)
   - [Lister les plats d'un restaurant](#lister-les-plats-dun-restaurant)
3. [Restaurants (protégé)](#restaurants-protégé)
   - [Créer une réservation](#créer-une-réservation)
   - [Lister mes réservations](#lister-mes-réservations)
   - [Afficher une réservation](#afficher-une-réservation)
4. [Hôtels (protégé)](#hôtels-protégé)
   - [Lister les hôtels](#lister-les-hôtels)
   - [Lister les chambres d'un hôtel](#lister-les-chambres-dun-hôtel)
5. [Réservations d'hôtel (protégé)](#réservations-dhôtel-protégé)
   - [Créer une réservation d'hôtel](#créer-une-réservation-dhôtel)
   - [Afficher une réservation d'hôtel](#afficher-une-réservation-dhôtel)
6. [Sites touristiques (protégé)](#sites-touristiques-protégé)
   - [Lister les sites](#lister-les-sites)
   - [Afficher un site](#afficher-un-site)
   - [Contacter un guide](#contacter-un-guide)

## Authentification

### Inscription
- **Méthode HTTP** : POST
- **Endpoint** : `/api/register`
- **Description** : Permet à un nouvel utilisateur de s'inscrire.
- **En-têtes requis** :
  - `Content-Type: application/json`
- **Corps de la requête** :
  ```json
  {
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "motdepasse",
    "password_confirmation": "motdepasse"
  }
  ```
- **Exemple de requête cURL** :
  ```bash
  curl -X POST http://localhost/api/register \
    -H "Content-Type: application/json" \
    -d '{
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "password": "motdepasse",
      "password_confirmation": "motdepasse"
    }'
  ```

### Connexion
- **Méthode HTTP** : POST
- **Endpoint** : `/api/login`
- **Description** : Permet à un utilisateur de se connecter et d'obtenir un token d'authentification.
- **En-têtes requis** :
  - `Content-Type: application/json`
- **Corps de la requête** :
  ```json
  {
    "email": "john@example.com",
    "password": "motdepasse"
  }
  ```
- **Exemple de requête cURL** :
  ```bash
  curl -X POST http://localhost/api/login \
    -H "Content-Type: application/json" \
    -d '{"email":"john@example.com","password":"motdepasse"}'
  ```

## Restaurants (public)

### Lister les restaurants
- **Méthode HTTP** : GET
- **Endpoint** : `/api/restaurants`
- **Description** : Récupère la liste de tous les restaurants.
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/restaurants
  ```

### Afficher un restaurant
- **Méthode HTTP** : GET
- **Endpoint** : `/api/restaurants/:id`
- **Description** : Récupère les détails d'un restaurant spécifique.
- **Paramètres d'URL** :
  - `id` : L'identifiant du restaurant
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/restaurants/1
  ```

### Lister les plats d'un restaurant
- **Méthode HTTP** : GET
- **Endpoint** : `/api/restaurants/:restaurant_id/dishes`
- **Description** : Récupère la liste des plats d'un restaurant spécifique.
- **Paramètres d'URL** :
  - `restaurant_id` : L'identifiant du restaurant
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/restaurants/1/dishes
  ```

## Restaurants (protégé)

### Créer une réservation
- **Méthode HTTP** : POST
- **Endpoint** : `/api/restaurants/:restaurant_id/reserve`
- **Description** : Crée une nouvelle réservation dans un restaurant.
- **Authentification requise** : Oui (Token JWT)
- **En-têtes requis** :
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- **Corps de la requête** :
  ```json
  {
    "reservation_at": "2025-10-01T19:30:00Z",
    "party_size": 4,
    "special_requests": "Table près de la fenêtre",
    "items": [
      { "dish_id": 12, "qty": 2 },
      { "dish_id": 18, "qty": 1 }
    ]
  }
  ```
- **Exemple de requête cURL** :
  ```bash
  curl -X POST http://localhost/api/restaurants/1/reserve \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer votre_token_jwt" \
    -d '{"reservation_at":"2025-10-01T19:30:00Z","party_size":4,"special_requests":"Table près de la fenêtre","items":[{"dish_id":12,"qty":2},{"dish_id":18,"qty":1}]}'
  ```

### Lister mes réservations
- **Méthode HTTP** : GET
- **Endpoint** : `/api/restaurant-reservations`
- **Description** : Récupère la liste des réservations de l'utilisateur connecté.
- **Authentification requise** : Oui (Token JWT)
- **En-têtes requis** :
  - `Authorization: Bearer <token>`
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/restaurant-reservations \
    -H "Authorization: Bearer votre_token_jwt"
  ```

### Afficher une réservation
- **Méthode HTTP** : GET
- **Endpoint** : `/api/restaurant-reservations/:id`
- **Description** : Récupère les détails d'une réservation spécifique.
- **Authentification requise** : Oui (Token JWT)
- **Paramètres d'URL** :
  - `id` : L'identifiant de la réservation
- **En-têtes requis** :
  - `Authorization: Bearer <token>`
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/restaurant-reservations/1 \
    -H "Authorization: Bearer votre_token_jwt"
  ```

## Hôtels (protégé)

### Lister les hôtels
- **Méthode HTTP** : GET
- **Endpoint** : `/api/hotels`
- **Description** : Récupère la liste des hôtels disponibles avec leurs chambres.
- **Authentification requise** : Oui (Token Sanctum)
- **En-têtes requis** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/hotels \
    -H "Authorization: Bearer votre_token_sanctum" \
    -H "Accept: application/json"
  ```
  
### Lister les chambres d'un hôtel
- **Méthode HTTP** : GET
- **Endpoint** : `/api/hotels/{hotel}/rooms`
- **Description** : Récupère la liste des chambres disponibles pour un hôtel spécifique.
- **Authentification requise** : Oui (Token Sanctum)
- **Paramètres d'URL** :
  - `hotel` : L'identifiant de l'hôtel
- **En-têtes requis** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/hotels/1/rooms \
    -H "Authorization: Bearer votre_token_sanctum" \
    -H "Accept: application/json"
  ```

## Réservations d'hôtel (protégé)

### Créer une réservation d'hôtel
- **Méthode HTTP** : POST
- **Endpoint** : `/api/bookings`
- **Description** : Crée une nouvelle réservation d'hôtel.
- **Authentification requise** : Oui (Token Sanctum)
- **En-têtes requis** :
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
- **Corps de la requête** :
  ```json
  {
    "hotel_id": 1,
    "room_id": 5,
    "check_in": "2025-11-15",
    "check_out": "2025-11-20",
    "guest_count": 2,
    "special_requests": "Chambre avec vue sur la mer si possible"
  }
  ```
- **Exemple de requête cURL** :
  ```bash
  curl -X POST http://localhost/api/bookings \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer votre_token_sanctum" \
    -H "Accept: application/json" \
    -d '{"hotel_id":1,"room_id":5,"check_in":"2025-11-15","check_out":"2025-11-20","guest_count":2,"special_requests":"Chambre avec vue sur la mer si possible"}'
  ```

### Afficher une réservation d'hôtel
- **Méthode HTTP** : GET
- **Endpoint** : `/api/bookings/{booking}`
- **Description** : Récupère les détails d'une réservation d'hôtel spécifique.
- **Authentification requise** : Oui (Token Sanctum)
- **Paramètres d'URL** :
  - `booking` : L'identifiant de la réservation
- **En-têtes requis** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/bookings/1 \
    -H "Authorization: Bearer votre_token_sanctum" \
    -H "Accept: application/json"
  ```

## Sites touristiques (protégé)

### Lister les sites
- **Méthode HTTP** : GET
- **Endpoint** : `/api/sites`
- **Description** : Récupère la liste des sites touristiques disponibles.
- **Authentification requise** : Oui (Token JWT)
- **En-têtes requis** :
  - `Authorization: Bearer <token>`
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/sites \
    -H "Authorization: Bearer votre_token_jwt"
  ```

### Afficher un site
- **Méthode HTTP** : GET
- **Endpoint** : `/api/sites/:site_id`
- **Description** : Récupère les détails d'un site touristique spécifique.
- **Authentification requise** : Oui (Token JWT)
- **Paramètres d'URL** :
  - `site_id` : L'identifiant du site touristique
- **En-têtes requis** :
  - `Authorization: Bearer <token>`
- **Exemple de requête cURL** :
  ```bash
  curl -X GET http://localhost/api/sites/1 \
    -H "Authorization: Bearer votre_token_jwt"
  ```

### Contacter un guide
- **Méthode HTTP** : POST
- **Endpoint** : `/api/sites/:site_id/contact-guide`
- **Description** : Envoie un message à un guide touristique pour un site spécifique.
- **Authentification requise** : Oui (Token JWT)
- **En-têtes requis** :
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- **Corps de la requête** :
  ```json
  {
    "name": "John",
    "email": "john@example.com",
    "phone": "+22660000000",
    "message": "Je souhaite organiser une visite."
  }
  ```
- **Exemple de requête cURL** :
  ```bash
  curl -X POST http://localhost/api/sites/1/contact-guide \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer votre_token_jwt" \
    -d '{"name":"John","email":"john@example.com","phone":"+22660000000","message":"Je souhaite organiser une visite."}'
  ```

## Variables d'environnement

Pour utiliser ces points de terminaison, vous devez configurer les variables suivantes :

- `base_url` : L'URL de base de l'API (par défaut : `http://localhost`)
- `token` : Le token Sanctum obtenu lors de la connexion (pour les endpoints protégés)

### Authentification

L'API utilise Sanctum pour l'authentification. Pour vous authentifier :

1. Effectuez une requête POST vers `/api/login` avec vos identifiants
2. Utilisez le token retourné dans l'en-tête `Authorization: Bearer <token>`

Exemple de réponse de connexion réussie :
```json
{
    "token": "1|abcdef123456...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

## Codes de statut HTTP

- `200 OK` : Requête traitée avec succès
- `201 Created` : Ressource créée avec succès
- `400 Bad Request` : Requête mal formée
- `401 Unauthorized` : Authentification requise
- `403 Forbidden` : Accès non autorisé
- `404 Not Found` : Ressource non trouvée
- `422 Unprocessable Entity` : Erreur de validation des données
- `500 Internal Server Error` : Erreur serveur

## Gestion des erreurs

En cas d'erreur, l'API renvoie une réponse au format JSON avec un message d'erreur descriptif. Par exemple :

```json
{
  "message": "Erreur de validation",
  "errors": {
    "email": ["Le champ email est obligatoire"],
    "password": ["Le mot de passe doit contenir au moins 8 caractères"]
  }
}
```

## Sécurité

- Tous les endpoints protégés nécessitent un token Sanctum valide dans l'enntête `Authorization: Bearer <token>`
- Les mots de passe sont stockés de manière sécurisée (hashés) dans la base de données
- Il est recommandé d'utiliser HTTPS en production
- Les tokens d'accès doivent être conservés de manière sécurisée et ne jamais être exposés côté client
- Pour les environnements de production, configurez les durées d'expiration appropriées pour les tokens
