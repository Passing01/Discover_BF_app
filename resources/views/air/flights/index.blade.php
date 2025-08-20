@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">Réserver un vol (Arrivée au Burkina Faso)</h1>

  <form method="get" class="panel-cream rounded-20 p-3 p-md-4 mb-4">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Aéroport d'origine</label>
        <select name="origin" class="form-select">
          <option value="">-- Tous --</option>
          @foreach($airports as $a)
            <option value="{{ $a->id }}" @selected(request('origin')===$a->id)>{{ $a->city }} — {{ $a->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Aéroport de destination</label>
        <select name="destination" class="form-select">
          <option value="">-- Tous --</option>
          @foreach($airports as $a)
            <option value="{{ $a->id }}" @selected(request('destination')===$a->id)>{{ $a->city }} — {{ $a->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Origine (IATA/Ville)</label>
        <input name="origin_iata" class="form-control" placeholder="ex: ABJ ou Abidjan" value="{{ request('origin_iata') }}" list="airports-origin-list">
        <datalist id="airports-origin-list">
          @foreach($airports as $a)
            <option value="{{ $a->iata_code }}">{{ $a->city }} — {{ $a->name }}</option>
            <option value="{{ $a->city }}">{{ $a->iata_code }} — {{ $a->name }}</option>
          @endforeach
        </datalist>
      </div>
      <div class="col-md-4">
        <label class="form-label">Destination (IATA/Ville)</label>
        <input name="destination_iata" class="form-control" placeholder="ex: OUA ou Ouagadougou" value="{{ request('destination_iata') }}" list="airports-dest-list">
        <datalist id="airports-dest-list">
          @foreach($airports as $a)
            <option value="{{ $a->iata_code }}">{{ $a->city }} — {{ $a->name }}</option>
            <option value="{{ $a->city }}">{{ $a->iata_code }} — {{ $a->name }}</option>
          @endforeach
        </datalist>
      </div>
      <div class="col-md-3">
        <label class="form-label">Date de départ</label>
        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
      </div>
      <div class="col-md-1">
        <button class="btn btn-orange w-100">Filtrer</button>
      </div>
    </div>
  </form>

  <div class="row g-3">
    @forelse($flights as $flight)
      <div class="col-md-4">
        <div class="panel-cream rounded-20 h-100 d-flex flex-column">
          @php($photo = optional($flight->destination)->photo_url)
          @if($photo)
            <img src="{{ $photo }}" alt="{{ $flight->destination->city }}" class="img-elevate" style="object-fit:cover; height:160px; width:100%; border-top-left-radius:20px; border-top-right-radius:20px;">
          @endif
          <div class="p-3 flex-grow-1">
            <h5 class="mb-2"><i class="bi bi-airplane me-1"></i>{{ $flight->origin->city }} → {{ $flight->destination->city }}</h5>
            <p class="mb-1"><strong>Compagnie:</strong> {{ $flight->airline ?? '—' }}</p>
            <p class="mb-1"><strong>Départ:</strong> {{ \Illuminate\Support\Carbon::parse($flight->departure_time)->format('d M Y, H:i') }}</p>
            <p class="mb-1"><strong>Arrivée:</strong> {{ \Illuminate\Support\Carbon::parse($flight->arrival_time)->format('d M Y, H:i') }}</p>
            <p class="mb-0"><strong>À partir de:</strong> {{ number_format($flight->base_price, 0, ',', ' ') }} XOF</p>
          </div>
          <div class="px-3 pb-3 d-flex gap-2 mt-auto">
            <a class="btn btn-cream flex-fill" href="{{ route('air.flights.show', $flight) }}">Détails</a>
            <a class="btn btn-orange flex-fill" href="{{ route('air.flights.book', $flight) }}">Réserver</a>
          </div>
        </div>
      </div>
    @empty
      <p>Aucun vol trouvé.</p>
    @endforelse
  </div>
  <div class="mt-3">{{ $flights->links() }}</div>
</div>
@endsection

@section('quick_actions')
  <a href="{{ route('air.bookings.index') }}" class="btn btn-cream btn-sm"><i class="bi bi-journal-check me-1"></i>Mes réservations</a>
  <a href="{{ route('air.flights.wizard') }}" class="btn btn-orange btn-sm"><i class="bi bi-shuffle me-1"></i>Planifier A/R</a>
@endsection
