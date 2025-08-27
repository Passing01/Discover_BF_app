# Intégration de Stripe

Ce document explique comment configurer et utiliser le système de paiement Stripe dans l'application.

## Configuration requise

1. Compte Stripe (https://dashboard.stripe.com/register)
2. Clés API Stripe (disponibles dans le tableau de bord Stripe)
3. PHP 8.0 ou supérieur
4. Laravel 10.x
5. Extension PHP cURL activée

## Installation

1. Installer le SDK Stripe PHP :
   ```bash
   composer require stripe/stripe-php
   ```

2. Publier la configuration Stripe :
   ```bash
   php artisan vendor:publish --tag=stripe-config
   ```

3. Configurer les variables d'environnement dans `.env` :
   ```env
   STRIPE_KEY=your-stripe-publishable-key
   STRIPE_SECRET=your-stripe-secret-key
   STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret
   STRIPE_WEBHOOK_TOLERANCE=300
   STRIPE_API_VERSION=2023-10-16
   ```

## Configuration du webhook

1. Allez dans le [tableau de bord Stripe](https://dashboard.stripe.com/webhooks)
2. Cliquez sur "Add endpoint"
3. Entrez l'URL de votre webhook : `https://votredomaine.com/payment/webhook`
4. Sélectionnez les événements à écouter :
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
5. Copiez la clé secrète du webhook dans votre fichier `.env`

## Test en mode développement

1. Utilisez les cartes de test Stripe :
   - Numéro : `4242 4242 4242 4242`
   - Date d'expiration : toute date future
   - CVC : n'importe quel code à 3 chiffres
   - Code postal : n'importe quel code postal

2. Pour tester les webhooks en local, utilisez Stripe CLI :
   ```bash
   stripe listen --forward-to localhost:8000/payment/webhook
   ```

## Flux de paiement

1. L'utilisateur sélectionne un restaurant et crée une réservation
2. Le système redirige vers la page de paiement
3. L'utilisateur entre ses informations de carte
4. Stripe traite le paiement
5. Le webhook met à jour le statut de la réservation

## Gestion des erreurs

Les erreurs sont enregistrées dans les journaux Laravel (`storage/logs/laravel.log`).

## Sécurité

- Ne jamais exposer les clés secrètes dans le code source
- Utiliser HTTPS en production
- Valider toutes les entrées utilisateur
- Mettre à jour régulièrement les dépendances

## Dépannage

### Problème : Erreur CSRF
- Vérifiez que la balise meta CSRF est présente dans le layout
- Vérifiez que le jeton CSRF est inclus dans les requêtes AJAX

### Problème : Webhook non reçu
- Vérifiez que l'URL du webhook est correcte
- Vérifiez que le serveur est accessible depuis Internet
- Vérifiez les journaux Stripe pour les erreurs

### Problème : Paiement échoué
- Vérifiez les logs Stripe
- Vérifiez que la carte de test est valide
- Vérifiez que le montant est correct

## Ressources

- [Documentation Stripe](https://stripe.com/docs)
- [SDK Stripe PHP](https://github.com/stripe/stripe-php)
- [Laravel Cashier](https://laravel.com/docs/billing)
