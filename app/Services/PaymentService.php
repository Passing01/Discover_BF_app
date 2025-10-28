<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Stripe;
use Stripe\HttpClient\CurlClient;
use App\Models\RestaurantReservation;
use App\Models\Dish;

class PaymentService
{
    protected ?StripeClient $stripe = null;

    public function __construct()
    {
        $secret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        $apiVersion = config('services.stripe.api_version') ?? env('STRIPE_API_VERSION');
        $caPath = env('STRIPE_CA_BUNDLE');
        $disableSslVerify = filter_var(env('STRIPE_DISABLE_SSL_VERIFY', false), FILTER_VALIDATE_BOOLEAN);

        if ($caPath) {
            // Normaliser le chemin Windows vers des slashs avant
            $caPath = str_replace('\\', '/', $caPath);
        }

        if ($caPath && is_file($caPath)) {
            // Forcer aussi au niveau PHP/cURL/openssl
            try { @ini_set('curl.cainfo', $caPath); } catch (\Throwable $e) {}
            try { @ini_set('openssl.cafile', $caPath); } catch (\Throwable $e) {}
            try { @putenv('CURL_CA_BUNDLE='.$caPath); } catch (\Throwable $e) {}
            try { @putenv('SSL_CERT_FILE='.$caPath); } catch (\Throwable $e) {}
            // Fixe le chemin CA au niveau de Stripe PHP (CurlClient) si disponible
            $curlClient = null;
            try { $curlClient = CurlClient::instance(); } catch (\Throwable $e) { $curlClient = null; }
            if ($curlClient && method_exists($curlClient, 'setCABundlePath')) {
                try { $curlClient->setCABundlePath($caPath); } catch (\Throwable $e) {}
            }

        // Option de contournement DEV UNIQUEMENT: désactiver la vérification SSL (dangereux en prod)
        if ($disableSslVerify && method_exists(Stripe::class, 'setVerifySslCerts')) {
            try {
                Stripe::setVerifySslCerts(false);
                Log::warning('[DEV ONLY] Vérification SSL Stripe désactivée via STRIPE_DISABLE_SSL_VERIFY. Ne pas utiliser en production.');
            } catch (\Throwable $e) {
                Log::warning('Impossible de désactiver la vérification SSL Stripe', ['error' => $e->getMessage()]);
            }
        }

            // Définit aussi via l'ancienne API statique si disponible
            if (method_exists(Stripe::class, 'setCABundlePath')) {
                try { Stripe::setCABundlePath($caPath); } catch (\Throwable $e) {}
            }
        }

        if ($secret) {
            $config = ['api_key' => $secret];
            if (!empty($apiVersion)) {
                $config['stripe_version'] = $apiVersion;
            }
            $this->stripe = new StripeClient($config);
        }
    }

    /**
     * Calcule le montant pour une réservation donnée en unité monétaire Stripe.
     * Note: XOF est une devise à zéro décimale chez Stripe, donc pas de x100.
     */
    public function calculateAmount(string $type, $reservation): int
    {
        switch ($type) {
            case 'restaurant':
                if (!$reservation instanceof RestaurantReservation) {
                    return 5000; // fallback si pas de réservation chargée
                }
                $items = $reservation->order_items ?? [];
                if (empty($items)) {
                    return 5000;
                }
                $quantities = [];
                $ids = [];
                foreach ($items as $row) {
                    $id = $row['dish_id'] ?? null;
                    $qty = (int)($row['qty'] ?? 0);
                    if ($id && $qty > 0) {
                        $ids[] = $id;
                        $quantities[$id] = ($quantities[$id] ?? 0) + $qty;
                    }
                }
                if (empty($ids)) return 5000;
                $dishes = Dish::whereIn('id', array_unique($ids))->get(['id','price']);
                $total = 0.0;
                foreach ($dishes as $dish) {
                    $qty = $quantities[$dish->id] ?? 0;
                    // price est stocké en FCFA (ex: 2500.00). XOF est zero-decimal pour Stripe
                    $total += ((float)$dish->price) * $qty; // total en FCFA
                }
                // Arrondir à l'entier le plus proche (XOF sans décimales)
                return (int) round($total);
            default:
                return 5000;
        }
    }

    /**
     * Crée une PaymentIntent avec un montant fourni (devise XOF zero-decimal).
     */
    public function createPaymentIntentWithAmount(int $amount, string $type, string $reservationId, string $userId): array
    {
        try {
            if ($this->stripe) {
                $intent = $this->stripe->paymentIntents->create([
                    'amount' => $amount, // XOF: entier en FCFA, pas de centimes
                    'currency' => 'xof',
                    'metadata' => [
                        'type' => $type,
                        'reservation_id' => (string) $reservationId,
                        'user_id' => (string) $userId,
                    ],
                ]);

                return [
                    'success' => true,
                    'client_secret' => $intent->client_secret,
                    'amount' => $amount,
                ];
            }

            // Fallback sans Stripe pour environnement de dev
            Log::warning('STRIPE_SECRET manquant, retour d\'un client_secret factice.');
            return [
                'success' => true,
                'client_secret' => 'test_client_secret_' . $reservationId,
                'amount' => $amount,
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur création PaymentIntent (with amount)', [
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crée une Payment Intent Stripe (ou une valeur factice en dev si clé absente).
     */
    public function createPaymentIntent(string $type, string $reservationId, string $userId): array
    {
        try {
            // DEV fake mode: bypass Stripe
            if (filter_var(env('STRIPE_DEV_FAKE_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
                $fakeAmount = $this->calculateAmount($type, null);
                Log::warning('[DEV FAKE MODE] PaymentIntent simulé. Aucune requête Stripe.');
                return [
                    'success' => true,
                    'client_secret' => 'test_client_secret_' . $reservationId,
                    'amount' => $fakeAmount,
                ];
            }
            // Montant simplifié pour le moment (sans récupérer le modèle complet)
            $amount = $this->calculateAmount($type, null);

            if ($this->stripe) {
                $intent = $this->stripe->paymentIntents->create([
                    'amount' => $amount,
                    'currency' => 'xof',
                    'metadata' => [
                        'type' => $type,
                        'reservation_id' => (string) $reservationId,
                        'user_id' => (string) $userId,
                    ],
                ]);

                return [
                    'success' => true,
                    'client_secret' => $intent->client_secret,
                    'amount' => $amount,
                ];
            }

            // Fallback sans Stripe pour environnement de dev
            Log::warning('STRIPE_SECRET manquant, retour d\'un client_secret factice.');
            return [
                'success' => true,
                'client_secret' => 'test_client_secret_' . $reservationId,
                'amount' => $amount,
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur création PaymentIntent', [
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Traite le webhook Stripe (placeholder minimal qui valide toujours).
     */
    public function handleWebhook(string $payload, ?string $signature): bool
    {
        // TODO: Valider la signature et traiter les événements Stripe.
        // Placeholder: considérer comme traité avec succès.
        return true;
    }

    /**
     * Crée une session Stripe Checkout (formulaire hébergé) et retourne son URL.
     */
    public function createCheckoutSession(string $type, $reservation, string $successUrl, string $cancelUrl): array
    {
        try {
            $amount = $this->calculateAmount($type, $reservation);

            if (!$this->stripe || filter_var(env('STRIPE_DEV_FAKE_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
                Log::warning('[DEV FAKE MODE] Checkout simulé: redirection directe vers success');
                return [
                    'success' => true,
                    'url' => $successUrl,
                ];
            }

            $session = $this->stripe->checkout->sessions->create([
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'xof',
                        'product_data' => [
                            'name' => ucfirst(str_replace('_',' ', $type)).' #'.$reservation->id,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'type' => $type,
                    'reservation_id' => (string) $reservation->id,
                    'user_id' => (string) ($reservation->user_id ?? ''),
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            return [
                'success' => true,
                'url' => $session->url,
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur création Checkout Session', [
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Récupère l'URL du reçu Stripe (receipt_url) à partir d'une session Checkout ou d'un PaymentIntent.
     */
    public function getReceiptUrl(?string $sessionId, ?string $paymentIntentId): ?string
    {
        try {
            if (!$this->stripe) {
                return null;
            }
            $piId = $paymentIntentId;
            if (!$piId && !empty($sessionId)) {
                $session = $this->stripe->checkout->sessions->retrieve($sessionId, []);
                $piId = $session->payment_intent ?? null;
            }
            if (!$piId) {
                return null;
            }

            $pi = $this->stripe->paymentIntents->retrieve($piId, ['expand' => ['charges']]);
            if (!empty($pi->charges) && !empty($pi->charges->data)) {
                $charge = $pi->charges->data[0];
                return $charge->receipt_url ?? null;
            }
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur récupération receipt_url', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
