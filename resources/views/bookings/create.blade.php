@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Agenda culturel</a></li>
      <li class="breadcrumb-item"><a href="{{ route('events.show', $event) }}">{{ $event->name }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Réservation</li>
    </ol>
  </nav>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="panel-cream rounded-20">
        <div class="p-3">
          <h1 class="mb-1">Réserver: {{ $event->name }}</h1>
          <div class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i>{{ $event->location }} • <i class="bi bi-calendar3 mx-1"></i>{{ $event->start_date }} → {{ $event->end_date }}</div>

          <form method="POST" action="{{ route('bookings.store', $event) }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" name="buyer_name" class="form-control @error('buyer_name') is-invalid @enderror" value="{{ old('buyer_name') ?? auth()->user()->name ?? '' }}" required>
                @error('buyer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="buyer_email" class="form-control @error('buyer_email') is-invalid @enderror" value="{{ old('buyer_email') ?? auth()->user()->email ?? '' }}" required>
                @error('buyer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <h5 class="mt-4">Choisissez vos billets</h5>
            <div class="vstack gap-2">
              @foreach($event->ticketTypes as $tt)
                <div class="row g-2 align-items-center border rounded p-2 bg-white">
                  <div class="col-md-6">
                    <div class="fw-semibold">{{ $tt->name }}</div>
                    @if(!empty($tt->description))<div class="text-muted small">{{ $tt->description }}</div>@endif
                    @if(!empty($tt->capacity))
                      <div class="text-muted small">Capacité: {{ $tt->capacity }}</div>
                    @endif
                  </div>
                  <div class="col-md-3 fw-semibold">{{ number_format($tt->price, 0, ',', ' ') }} CFA</div>
                  <div class="col-md-3">
                    <input type="number" min="0" max="{{ $tt->capacity ?? 50 }}" name="quantities[{{ $tt->id }}]" class="form-control js-qty" value="{{ old('quantities.'.$tt->id, 0) }}" data-price="{{ (int)($tt->price ?? 0) }}" data-name="{{ $tt->name }}">
                    <div class="form-text">Quantité</div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="mt-3 d-flex gap-2">
              <button id="js-submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i>Confirmer la réservation</button>
              <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="panel-cream rounded-20 position-sticky" style="top:100px;">
        <div class="px-3 py-2 fw-semibold">Récapitulatif</div>
        <div class="p-3">
          @if(!empty($event->image_path))
            <img src="{{ asset('storage/'.$event->image_path) }}" alt="{{ $event->name }}" class="w-100 rounded mb-2" style="max-height:150px;object-fit:cover;">
          @endif
          <div class="small text-muted mb-2">{{ $event->location }} • {{ $event->start_date }} → {{ $event->end_date }}</div>
          <ul id="js-lines" class="list-unstyled small mb-2 text-muted">
            <li>Aucun billet sélectionné</li>
          </ul>
          <div class="d-flex justify-content-between align-items-center fw-semibold">
            <span>Total</span>
            <span id="js-total">0 CFA</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const qtyInputs = Array.from(document.querySelectorAll('.js-qty'));
    const totalEl = document.getElementById('js-total');
    const submitBtn = document.getElementById('js-submit');
    const linesEl = document.getElementById('js-lines');
    function formatCFA(n){
      try { return new Intl.NumberFormat('fr-FR').format(n) + ' CFA'; } catch(e){ return (n||0) + ' CFA'; }
    }
    function compute(){
      let sum = 0;
      const rows = [];
      qtyInputs.forEach(inp => {
        const q = parseInt(inp.value || '0', 10) || 0;
        const p = parseInt(inp.dataset.price || '0', 10) || 0;
        const name = inp.dataset.name || '';
        const max = parseInt(inp.getAttribute('max') || '0', 10) || 0;
        if (max && q > max) { inp.value = max; }
        sum += q * p;
        if (q > 0) {
          rows.push(`<li class=\"d-flex justify-content-between\"><span>${name} × ${q}</span><span>${formatCFA(q*p)}</span></li>`);
        }
      });
      totalEl.textContent = formatCFA(sum);
      if (linesEl) {
        linesEl.innerHTML = rows.length ? rows.join('') : '<li>Aucun billet sélectionné</li>';
      }
      if (sum <= 0) {
        submitBtn.setAttribute('disabled', 'disabled');
      } else {
        submitBtn.removeAttribute('disabled');
      }
    }
    qtyInputs.forEach(inp => inp.addEventListener('input', compute));
    compute();
  });
</script>
@endpush
