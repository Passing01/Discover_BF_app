@extends('layouts.tourist')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Paiement</h1>

    <div class="bg-white rounded shadow p-4">
        <p class="mb-2">Type: <strong>{{ ucfirst(str_replace('_',' ', $type)) }}</strong></p>
        <p class="mb-2">Réservation #<strong>{{ $reservation->id }}</strong></p>
        <p class="mb-4">Montant à payer: <strong>{{ number_format($amount, 2, ',', ' ') }}</strong></p>

        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('payment.checkout', ['type' => $type, 'id' => $reservation->id]) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Payer avec Stripe</button>
            </form>

            @if(!empty($stripeKey))
                <details class="mt-4">
                    <summary class="cursor-pointer text-sm text-gray-700">Payer via carte intégrée (Stripe Elements)</summary>
                    <div class="mt-3">
                        <form id="payment-form" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Carte</label>
                                <div id="card-element" class="border rounded p-3"></div>
                                <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>
                            </div>
                            <button id="pay-button" type="submit" class="px-4 py-2 bg-gray-800 text-white rounded">Payer par carte</button>
                        </form>
                    </div>
                </details>
            @else
                <div class="bg-yellow-100 text-yellow-800 p-2 rounded">STRIPE_KEY manquante: le paiement via carte intégrée est désactivé. Le bouton "Payer avec Stripe" reste disponible.</div>
            @endif
        </div>

        <div class="mt-6 flex gap-2">
            <a href="{{ route('payment.cancel', ['type' => $type, 'id' => $reservation->id]) }}" class="px-4 py-2 bg-gray-200 rounded">Annuler</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(!empty($stripeKey))
<script src="https://js.stripe.com/v3/"></script>
<script>
  (function() {
    const stripe = Stripe(@json($stripeKey));
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    const payBtn = document.getElementById('pay-button');
    const errorEl = document.getElementById('card-errors');

    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      errorEl.textContent = '';
      payBtn.disabled = true;
      payBtn.textContent = 'Traitement...';

      try {
        const resp = await fetch(@json(route('payment.intent')), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token()),
          },
          body: JSON.stringify({
            type: @json($type),
            reservation_id: @json($reservation->id),
          }),
        });

        const data = await resp.json();
        if (!resp.ok) {
          throw new Error(data.error || 'Erreur lors de la création du paiement');
        }

        const result = await stripe.confirmCardPayment(data.clientSecret, {
          payment_method: { card }
        });

        if (result.error) {
          throw new Error(result.error.message);
        }

        window.location.href = @json(route('payment.success', ['type' => $type, 'id' => $reservation->id]));
      } catch (err) {
        errorEl.textContent = err.message || 'Une erreur est survenue.';
      } finally {
        payBtn.disabled = false;
        payBtn.textContent = 'Payer';
      }
    });
  })();
<\/script>
@endif
@endsection
