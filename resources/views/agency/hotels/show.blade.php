@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <h1 class="mb-0 text-orange">{{ $hotel->name }}</h1>
    <a href="{{ route('agency.hotels.edit', $hotel) }}" class="btn btn-cream ms-auto">Modifier</a>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="panel-cream rounded-20 h-100 p-3 p-md-4">
          <h5 class="mb-3">Informations</h5>
          <div class="mb-2"><strong>Adresse</strong><br>{{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->country }}</div>
          <div class="mb-2"><strong>Contact</strong><br>{{ $hotel->phone }} — {{ $hotel->email }}</div>
          <div class="mb-2"><strong>Coordonnées</strong><br>Lat: {{ $hotel->latitude }}, Lng: {{ $hotel->longitude }}</div>
          <div class="mb-2"><strong>Équipements</strong><br>
            @forelse($hotel->amenities as $a)
              <span class="badge badge-soft me-1 mb-1">{{ $a->name }}</span>
            @empty
              <span class="text-muted">Aucun</span>
            @endforelse
          </div>
          <div class="mb-2"><strong>Règles du séjour</strong><br>
            @forelse($hotel->rules as $r)
              <span class="badge badge-soft me-1 mb-1">{{ $r->name }}</span>
            @empty
              <span class="text-muted">Aucune</span>
            @endforelse
          </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel-cream rounded-20 h-100 p-3 p-md-4">
          <h5 class="mb-3">Actions</h5>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-orange" href="{{ route('agency.rooms.create', $hotel) }}">Ajouter une chambre</a>
            <a class="btn btn-cream" href="{{ route('agency.reservations.index', ['hotel_id' => $hotel->id]) }}">Voir les réservations</a>
            <a class="btn btn-cream" href="{{ route('agency.hotels.index') }}">Retour à la liste</a>
          </div>
      </div>
    </div>
  </div>

  <div class="panel-cream rounded-20 p-3 p-md-4">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
      <h5 class="mb-0 me-auto">Chambres ({{ $hotel->rooms->count() }})</h5>
      <div class="d-flex gap-2 align-items-end flex-wrap">
        <div>
          <label class="form-label small mb-1">Rechercher</label>
          <input id="roomSearch" type="text" class="form-control form-control-sm" placeholder="Nom ou type">
        </div>
        <div>
          <label class="form-label small mb-1">Type</label>
          <select id="roomType" class="form-select form-select-sm">
            <option value="">Tous</option>
            @php($types = $hotel->rooms->pluck('type')->filter()->unique()->values())
            @foreach($types as $tp)
              <option value="{{ $tp }}">{{ $tp }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label small mb-1">Trier</label>
          <select id="roomSort" class="form-select form-select-sm">
            <option value="price_asc">Prix ↑</option>
            <option value="price_desc">Prix ↓</option>
            <option value="capacity_desc">Capacité ↓</option>
            <option value="capacity_asc">Capacité ↑</option>
          </select>
        </div>
        <div class="d-flex align-items-end gap-1">
          <button id="viewGrid" type="button" class="btn btn-cream btn-sm">Grille</button>
          <button id="viewTable" type="button" class="btn btn-cream btn-sm">Tableau</button>
        </div>
      </div>
    </div>
      @if($hotel->rooms->count() === 0)
        <div class="alert alert-info rounded-20">Aucune chambre. Ajoutez votre première chambre.</div>
      @else
        <!-- Grid view -->
        <div id="roomsGrid" class="row g-3">
          @foreach($hotel->rooms as $room)
            <div class="col-12 col-md-6 col-lg-4 room-item"
                 data-name="{{ Str::lower($room->name.' '.$room->type) }}"
                 data-type="{{ Str::lower($room->type) }}"
                 data-capacity="{{ (int) $room->capacity }}"
                 data-price="{{ (int) $room->price_per_night }}">
              <div class="panel-cream rounded-20 h-100 overflow-hidden">
                @php($roomPhoto = $room->photo ? asset('storage/'.$room->photo) : null)
                <div class="ratio ratio-16x9" style="background:#f2e9e1;">
                  @if($roomPhoto)
                    <div style="background-image:url('{{ $roomPhoto }}');background-size:cover;background-position:center;width:100%;height:100%;"></div>
                  @else
                    <div class="d-flex align-items-center justify-content-center w-100 h-100 text-muted" style="font-size: 0.9rem;">
                      <i class="bi bi-image me-2"></i> Pas de photo
                    </div>
                  @endif
                </div>
                <div class="p-3 p-md-4">
                  <h6 class="mb-1">{{ $room->name }}</h6>
                  <div class="small text-muted d-flex gap-2 flex-wrap mb-2">
                    @if(!empty($room->type))
                      <span class="badge badge-soft">{{ $room->type }}</span>
                    @endif
                    <span class="badge badge-soft">{{ $room->capacity }} pers.</span>
                  </div>
                  <div class="fw-semibold mb-3">{{ number_format((int) $room->price_per_night, 0, ',', ' ') }} CFA / nuit</div>
                  <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-cream btn-sm" href="{{ route('agency.reservations.index', ['hotel_id' => $hotel->id]) }}">Réservations</a>
                    @if(Route::has('agency.rooms.edit'))
                      <a class="btn btn-cream btn-sm" href="{{ route('agency.rooms.edit', [$hotel, $room]) }}">Modifier</a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Table view (hidden by default) -->
        <div id="roomsTable" class="table-responsive d-none mt-2">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Capacité</th>
                <th>Prix/nuit</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($hotel->rooms as $room)
                <tr class="room-row"
                    data-name="{{ Str::lower($room->name.' '.$room->type) }}"
                    data-type="{{ Str::lower($room->type) }}"
                    data-capacity="{{ (int) $room->capacity }}"
                    data-price="{{ (int) $room->price_per_night }}">
                  <td>{{ $room->name }}</td>
                  <td>{{ $room->type }}</td>
                  <td>{{ $room->capacity }}</td>
                  <td>{{ number_format((int) $room->price_per_night, 0, ',', ' ') }} CFA</td>
                  <td>
                    <a class="btn btn-sm btn-cream" href="{{ route('agency.reservations.index', ['hotel_id' => $hotel->id]) }}">Réservations</a>
                    @if(Route::has('agency.rooms.edit'))
                      <a class="btn btn-sm btn-cream" href="{{ route('agency.rooms.edit', [$hotel, $room]) }}">Modifier</a>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
  </div>
  <script>
    (function() {
      const search = document.getElementById('roomSearch');
      const typeSel = document.getElementById('roomType');
      const sortSel = document.getElementById('roomSort');
      const grid = document.getElementById('roomsGrid');
      const table = document.getElementById('roomsTable');
      const btnGrid = document.getElementById('viewGrid');
      const btnTable = document.getElementById('viewTable');

      function getItems() {
        return Array.from(grid.querySelectorAll('.room-item'));
      }
      function applyFilters() {
        const q = (search?.value || '').trim().toLowerCase();
        const t = (typeSel?.value || '').toLowerCase();
        getItems().forEach(el => {
          const name = el.dataset.name || '';
          const type = el.dataset.type || '';
          const okQ = !q || name.includes(q);
          const okT = !t || type === t;
          el.classList.toggle('d-none', !(okQ && okT));
        });
        // Also apply to table rows
        document.querySelectorAll('.room-row').forEach(tr => {
          const name = tr.dataset.name || '';
          const type = tr.dataset.type || '';
          const okQ = !q || name.includes(q);
          const okT = !t || type === t;
          tr.classList.toggle('d-none', !(okQ && okT));
        });
      }
      function applySort() {
        const val = sortSel?.value || 'price_asc';
        const items = getItems();
        items.sort((a,b)=>{
          const pa = parseInt(a.dataset.price||'0',10);
          const pb = parseInt(b.dataset.price||'0',10);
          const ca = parseInt(a.dataset.capacity||'0',10);
          const cb = parseInt(b.dataset.capacity||'0',10);
          switch(val){
            case 'price_desc': return pb - pa;
            case 'capacity_desc': return cb - ca;
            case 'capacity_asc': return ca - cb;
            case 'price_asc': default: return pa - pb;
          }
        });
        items.forEach(el => grid.appendChild(el));
        // Table sort
        const rows = Array.from(document.querySelectorAll('#roomsTable tbody .room-row'));
        rows.sort((a,b)=>{
          const pa = parseInt(a.dataset.price||'0',10);
          const pb = parseInt(b.dataset.price||'0',10);
          const ca = parseInt(a.dataset.capacity||'0',10);
          const cb = parseInt(b.dataset.capacity||'0',10);
          switch(val){
            case 'price_desc': return pb - pa;
            case 'capacity_desc': return cb - ca;
            case 'capacity_asc': return ca - cb;
            case 'price_asc': default: return pa - pb;
          }
        });
        const tbody = document.querySelector('#roomsTable tbody');
        rows.forEach(r => tbody.appendChild(r));
      }
      function setView(mode){
        if(mode==='table'){
          grid.classList.add('d-none');
          table.classList.remove('d-none');
        } else {
          table.classList.add('d-none');
          grid.classList.remove('d-none');
        }
      }
      search?.addEventListener('input', applyFilters);
      typeSel?.addEventListener('change', applyFilters);
      sortSel?.addEventListener('change', applySort);
      btnGrid?.addEventListener('click', ()=>setView('grid'));
      btnTable?.addEventListener('click', ()=>setView('table'));
      // initial
      applyFilters();
      applySort();
    })();
  </script>
</div>
@endsection
