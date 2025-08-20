@extends('layouts.tourist')

@section('content')
<div class="py-2">
  <div class="d-flex align-items-center mb-3">
    <h1 class="mb-0 text-orange">Mes hôtels</h1>
    <a href="{{ route('agency.hotels.create') }}" class="btn btn-orange ms-auto"><i class="bi bi-plus-lg me-1"></i> Nouvel hôtel</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <!-- Filter toolbar -->
  <form method="GET" class="panel-cream rounded-20 p-3 p-md-4 mb-3">
    <div class="row g-2 align-items-end">
      <div class="col-md-6">
        <label class="form-label">Rechercher (nom, ville…)</label>
        <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Ex: Ouagadougou, Soleil…">
      </div>
      <div class="col-md-3">
        <label class="form-label">Trier par</label>
        <select name="sort" class="form-select">
          <option value="recent" @selected(request('sort')==='recent')>Plus récents</option>
          <option value="rooms" @selected(request('sort')==='rooms')>Plus de chambres</option>
          <option value="stars" @selected(request('sort')==='stars')>Mieux notés</option>
        </select>
      </div>
      <div class="col-md-3 d-grid">
        <button class="btn btn-cream">Appliquer</button>
      </div>
    </div>
  </form>

  <div class="row g-3">
    @forelse($hotels as $hotel)
      <div class="col-12 col-md-6 col-lg-4">
        <div class="panel-cream rounded-20 h-100 overflow-hidden">
          @php($photoUrl = $hotel->photo ? asset('storage/'.$hotel->photo) : null)
          @if($photoUrl)
            <div class="ratio ratio-16x9" style="background:#f2e9e1;">
              <div style="background-image:url('{{ $photoUrl }}');background-size:cover;background-position:center;
                          border-bottom:1px solid rgba(0,0,0,.05);
                          width:100%;height:100%;"></div>
            </div>
          @endif
          <div class="p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="pe-2">
                <h5 class="mb-1">{{ $hotel->name }}</h5>
                <div class="small text-muted d-flex align-items-center gap-2 flex-wrap">
                  <span class="badge badge-soft">{{ $hotel->city }}, {{ $hotel->country }}</span>
                  <span class="badge badge-soft">{{ $hotel->rooms->count() }} chambre(s)</span>
                  @php($stars = (int)($hotel->stars ?? 0))
                  @if($stars > 0)
                    <span class="badge badge-soft">
                      @for($i=0;$i<$stars;$i++) ★ @endfor
                    </span>
                  @endif
                </div>
              </div>
            </div>
            @if(!empty($hotel->description))
              <p class="mt-2 mb-3">{{ Str::limit($hotel->description, 120) }}</p>
            @endif
            <div class="d-flex gap-2">
              <a href="{{ route('agency.hotels.show', $hotel) }}" class="btn btn-cream btn-sm">Détails</a>
              <a href="{{ route('agency.rooms.create', $hotel) }}" class="btn btn-orange btn-sm">Ajouter une chambre</a>
              <a href="{{ route('agency.hotels.edit', $hotel) }}" class="btn btn-cream btn-sm">Modifier</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info rounded-20">Aucun hôtel géré.</div></div>
    @endforelse
  </div>
</div>
@endsection
