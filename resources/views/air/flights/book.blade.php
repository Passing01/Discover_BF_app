@push('styles')
  <link rel="stylesheet" href="{{ asset('css/flight-book.css') }}">
@endpush
@extends('layouts.tourist')

@section('content')
<div class="container flight-book-container">
  <h2 class="flight-book-title">Réserver: {{ $flight->origin->city }} → {{ $flight->destination->city }}</h2>
  <div class="row g-4">
    <div class="col-12 col-lg-7 mb-4 mb-lg-0">
      <div class="flight-book-card p-3 p-md-4 mb-3">
          <form method="post" action="{{ route('air.flights.book.store', $flight) }}">
            @csrf

            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label fw-semibold text-primary">Adultes</label>
                <input type="number" name="adult_count" min="1" value="{{ old('adult_count', 1) }}" class="form-control rounded-3 shadow-sm border-0 bg-light @error('adult_count') is-invalid @enderror">
                @error('adult_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold text-primary">Enfants</label>
                <input type="number" name="child_count" min="0" value="{{ old('child_count', 0) }}" class="form-control rounded-3 shadow-sm border-0 bg-light @error('child_count') is-invalid @enderror">
                @error('child_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold text-primary">Bébés</label>
                <input type="number" name="infant_count" min="0" value="{{ old('infant_count', 0) }}" class="form-control rounded-3 shadow-sm border-0 bg-light @error('infant_count') is-invalid @enderror">
                @error('infant_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold text-primary">Bagages</label>
                <input type="number" name="baggage_count" min="0" value="{{ old('baggage_count', 0) }}" class="form-control rounded-3 shadow-sm border-0 bg-light @error('baggage_count') is-invalid @enderror">
                @error('baggage_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold text-primary">Classe</label>
                <select name="class" class="form-select rounded-3 shadow-sm border-0 bg-light @error('class') is-invalid @enderror">
                  <option value="economy" @selected(old('class')==='economy')>Économie</option>
                  <option value="business" @selected(old('class')==='business')>Affaires</option>
                  <option value="first" @selected(old('class')==='first')>Première</option>
                </select>
                @error('class')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3">Contact</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold text-primary">Nom complet</label>
                <input name="contact_name" class="form-control rounded-3 shadow-sm border-0 bg-light @error('contact_name') is-invalid @enderror" value="{{ old('contact_name') }}">
                @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-primary">Email</label>
                <input type="email" name="contact_email" class="form-control rounded-3 shadow-sm border-0 bg-light @error('contact_email') is-invalid @enderror" value="{{ old('contact_email', auth()->user()->email ?? '') }}">
                @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-primary">Téléphone</label>
                <input name="contact_phone" class="form-control rounded-3 shadow-sm border-0 bg-light @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone') }}">
                @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3">Détails des passagers</h5>
            <p class="text-muted">Renseignez les informations des passagers pour faciliter l'enregistrement.</p>
            <div id="passengers-container" class="vstack gap-3"></div>

            <div class="mt-2">
              <button type="button" id="generate-passengers" class="btn btn-cream">Générer les formulaires passagers</button>
            </div>

            <div class="mt-4 d-flex gap-2">
              <a href="{{ route('air.flights.index') }}" class="btn btn-cream">Annuler</a>
              <button class="btn btn-orange">Confirmer la réservation</button>
            </div>
          </form>
      </div>
    </div>
    <div class="col-12 col-lg-5">
      <div class="flight-book-summary p-3 p-md-4 mb-3">
        <h5 class="flight-summary-title mb-3">Résumé du vol</h5>
        <ul class="list-unstyled mb-3">
          <li class="d-flex align-items-center mb-2">
            <i class="bi bi-airplane me-2 text-primary"></i>
            <span><strong>Compagnie:</strong> {{ $flight->airline ?? '—' }} ({{ $flight->flight_number ?? '—' }})</span>
          </li>
          <li class="d-flex align-items-center mb-2">
            <i class="bi bi-clock me-2 text-primary"></i>
            <span><strong>Départ:</strong> {{ \Illuminate\Support\Carbon::parse($flight->departure_time)->format('d M Y, H:i') }}</span>
          </li>
          <li class="d-flex align-items-center mb-2">
            <i class="bi bi-geo-alt me-2 text-primary"></i>
            <span><strong>Arrivée:</strong> {{ \Illuminate\Support\Carbon::parse($flight->arrival_time)->format('d M Y, H:i') }}</span>
          </li>
        </ul>
        <div class="d-flex align-items-center mb-2">
          <i class="bi bi-cash-coin me-2 text-success"></i>
          <span class="fs-5 fw-bold text-success">{{ number_format($flight->base_price, 0, ',', ' ') }} XOF</span>
        </div>
        <div class="d-flex align-items-center">
          <i class="bi bi-people me-2 text-primary"></i>
          <span><strong>Places restantes:</strong> {{ $flight->seats_available }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('quick_actions')
  <a href="#" class="btn btn-orange btn-sm" data-submit-first-form><i class="bi bi-check2-circle me-1"></i>Confirmer</a>
  <a href="{{ route('air.bookings.index') }}" class="btn btn-cream btn-sm"><i class="bi bi-journal-check me-1"></i>Mes réservations</a>
@endsection

@push('scripts')
<script>
  function buildPassengerCard(index, type) {
    return `
  <div class="flight-book-passenger p-3 mb-3">
        <h6 class="mb-3">Passager #${index+1} <span class="badge bg-secondary">${type}</span></h6>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold text-primary">Prénom</label>
            <input name="passengers[${index}][first_name]" class="form-control rounded-3 shadow-sm border-0 bg-light" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold text-primary">Nom</label>
            <input name="passengers[${index}][last_name]" class="form-control rounded-3 shadow-sm border-0 bg-light" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold text-primary">Date de naissance</label>
            <input type="date" name="passengers[${index}][birthdate]" class="form-control rounded-3 shadow-sm border-0 bg-light" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold text-primary">Sexe</label>
            <select name="passengers[${index}][sex]" class="form-select rounded-3 shadow-sm border-0 bg-light" required>
              <option value="M">Homme</option>
              <option value="F">Femme</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold text-primary">Document</label>
            <select name="passengers[${index}][document_type]" class="form-select rounded-3 shadow-sm border-0 bg-light" required>
              <option value="PASS">Passeport</option>
              <option value="CNI">CNI</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-primary">Numéro du document</label>
            <input name="passengers[${index}][document_number]" class="form-control rounded-3 shadow-sm border-0 bg-light" required>
          </div>
        </div>
    </div>`;
  }

  function regeneratePassengerForms() {
    const adults = parseInt(document.querySelector('[name="adult_count"]').value || '1');
    const children = parseInt(document.querySelector('[name="child_count"]').value || '0');
    const infants = parseInt(document.querySelector('[name="infant_count"]').value || '0');
    const container = document.getElementById('passengers-container');
    container.innerHTML = '';
    let idx = 0;
    for (let i=0;i<adults;i++) container.insertAdjacentHTML('beforeend', buildPassengerCard(idx++, 'Adulte'));
    for (let i=0;i<children;i++) container.insertAdjacentHTML('beforeend', buildPassengerCard(idx++, 'Enfant'));
    for (let i=0;i<infants;i++) container.insertAdjacentHTML('beforeend', buildPassengerCard(idx++, 'Bébé'));
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('generate-passengers').addEventListener('click', regeneratePassengerForms);
  });
</script>
@endpush
