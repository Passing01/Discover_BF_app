<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantReservationResource;
use App\Models\RestaurantReservation;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantReservationController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Lister les réservations de l'utilisateur connecté (protégé Sanctum).
     */
    public function index(Request $request)
    {
        $reservations = RestaurantReservation::with('restaurant')
            ->where('user_id', Auth::id())
            ->orderByDesc('reservation_at')
            ->paginate(10);

        return RestaurantReservationResource::collection($reservations);
    }

    /**
     * Afficher une réservation spécifique appartenant à l'utilisateur (protégé Sanctum).
     */
    public function show(RestaurantReservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load('restaurant');

        return new RestaurantReservationResource($reservation);
    }

    /**
     * Retourner l'URL du reçu Stripe (receipt_url) pour une réservation (protégé Sanctum).
     * Paramètre optionnel: session_id (de Stripe Checkout) pour aider à retrouver le PaymentIntent.
     */
    public function receipt(Request $request, RestaurantReservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $sessionId = $request->query('session_id');
        $receiptUrl = $this->paymentService->getReceiptUrl($sessionId, $reservation->payment_intent_id ?? null);

        return response()->json([
            'reservation_id' => $reservation->id,
            'receipt_url' => $receiptUrl,
        ]);
    }
}
