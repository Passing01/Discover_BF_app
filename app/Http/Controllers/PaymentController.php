<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Models\RestaurantReservation;
use App\Models\HotelReservation;
use App\Models\EventBooking;
use App\Models\FlightReservation;
use App\Models\BusBooking;
use App\Models\TaxiReservation;
use App\Models\TouristSiteBooking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function showPaymentForm($type, $reservationId)
    {
        $reservation = $this->getReservation($type, $reservationId);
        
        if ($reservation->payment_status === 'paid') {
            return redirect()->route('payment.success', ['type' => $type, 'id' => $reservationId])
                ->with('status', 'Cette réservation a déjà été payée.');
        }

        return view('payments.form', [
            'reservation' => $reservation,
            'type' => $type,
            // XOF est zero-decimal: afficher l'entier FCFA
            'amount' => $this->paymentService->calculateAmount($type, $reservation),
            'stripeKey' => config('services.stripe.key') ?? env('STRIPE_KEY'),
        ]);
    }

    protected function getReservation($type, $id)
    {
        $model = $this->getModel($type);
        $with = $this->getRelations($type);
        
        return $model::with($with)
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    protected function getModel($type)
    {
        $models = [
            'restaurant' => RestaurantReservation::class,
            'hotel' => HotelReservation::class,
            'event' => EventBooking::class,
            'flight' => FlightReservation::class,
            'bus' => BusBooking::class,
            'taxi' => TaxiReservation::class,
            'tourist_site' => TouristSiteBooking::class,
        ];

        if (!isset($models[$type])) {
            abort(404, 'Type de réservation non trouvé');
        }

        return $models[$type];
    }

    protected function getRelations($type)
    {
        return [
            'restaurant' => ['restaurant'],
            'hotel' => ['hotel', 'room'],
            'event' => ['event'],
            'flight' => ['flight'],
            'bus' => ['trip'],
            'taxi' => [],
            'tourist_site' => ['site'],
        ][$type] ?? [];
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:restaurant,hotel,event,flight,bus,taxi,tourist_site',
                'reservation_id' => 'required',
            ]);

            $type = $request->type;
            $reservationId = $request->reservation_id;
            $userId = Auth::id();

            Log::info('Création d\'intention de paiement', [
                'type' => $type,
                'reservation_id' => $reservationId,
                'user_id' => $userId
            ]);

            // Calculer le montant exact à partir de la réservation
            $reservation = $this->getReservation($type, $reservationId);
            $amount = $this->paymentService->calculateAmount($type, $reservation);
            $result = $this->paymentService->createPaymentIntentWithAmount($amount, $type, $reservationId, $userId);

            if (!$result['success']) {
                return response()->json([
                    'error' => $result['error']
                ], 400);
            }

            return response()->json([
                'clientSecret' => $result['client_secret'],
                'amount' => $result['amount']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation', ['errors' => $e->errors()]);
            return response()->json([
                'error' => 'Données de réservation invalides.',
                'errors' => $e->errors(),
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erreur création intention de paiement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création du paiement.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function success(Request $request, $type, $id)
    {
        $reservation = $this->getReservation($type, $id);
        
        // Vérifier si le paiement est en attente ou déjà payé
        if ($reservation->payment_status === 'pending' || $reservation->payment_status === 'paid') {
            // Mettre à jour le statut si ce n'est pas déjà fait
            if ($reservation->payment_status === 'pending') {
                $reservation->update([
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);
            }

            // Tenter de récupérer une facture/receipt Stripe via la session de checkout
            $receiptUrl = null;
            try {
                $sessionId = $request->query('session_id');
                if (!empty($sessionId) && property_exists($this, 'paymentService')) {
                    // Utiliser le client Stripe interne si disponible
                    $reflect = new \ReflectionClass($this->paymentService);
                    $prop = $reflect->getProperty('stripe');
                    $prop->setAccessible(true);
                    $stripe = $prop->getValue($this->paymentService);
                    if ($stripe) {
                        $session = $stripe->checkout->sessions->retrieve($sessionId, []);
                        if (!empty($session->payment_intent)) {
                            $pi = $stripe->paymentIntents->retrieve($session->payment_intent, ['expand' => ['charges']]);
                            if (!empty($pi->charges) && !empty($pi->charges->data)) {
                                $charge = $pi->charges->data[0];
                                $receiptUrl = $charge->receipt_url ?? null;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignorer silencieusement si indisponible; on affichera quand même la page de succès
            }

            // Afficher une page de succès avec lien de facture/receipt si disponible
            return view('payments.success', [
                'reservation' => $reservation,
                'type' => $type,
                'receiptUrl' => $receiptUrl,
            ]);
        }

        // Rediriger vers le formulaire si le statut n'est ni pending ni paid
        return redirect()->route('payment.form', ['type' => $type, 'id' => $id])
            ->with('error', 'Le paiement n\'a pas été validé ou a échoué.');
    }

    public function cancel(Request $request, $type, $id)
    {
        $reservation = $this->getReservation($type, $id);
        
        return view('payments.cancel', [
            'reservation' => $reservation,
            'type' => $type
        ]);
    }

    public function startCheckout(Request $request, $type, $id)
    {
        $reservation = $this->getReservation($type, $id);
        
        // Inclure l'identifiant de session pour récupérer la facture/receipt côté success
        $successUrl = route('payment.success', ['type' => $type, 'id' => $id]) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('payment.cancel', ['type' => $type, 'id' => $id]);
        
        $result = $this->paymentService->createCheckoutSession($type, $reservation, $successUrl, $cancelUrl);
        
        if (!$result['success']) {
            return back()->with('error', $result['error'] ?? 'Impossible de démarrer Stripe Checkout.');
        }
        
        return redirect()->away($result['url']);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        
        try {
            $result = $this->paymentService->handleWebhook($payload, $signature);
            
            if ($result) {
                return response()->json(['status' => 'success']);
            }
            
            return response()->json(['status' => 'error', 'message' => 'Erreur de traitement'], 400);
            
        } catch (\Exception $e) {
            Log::error('Erreur webhook Stripe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
