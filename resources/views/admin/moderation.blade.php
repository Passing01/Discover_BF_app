@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title"><i class="bi bi-shield-check"></i> Modération</h4>
  </div>
  @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> <span>{{ session('status') }}</span></div>
  @endif

  <div class="row g-3">
    <div class="col-12">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="card-title text-light mb-0"><i class="bi bi-shop"></i> Restaurants récents</h6>
          <input type="text" class="form-control form-control-sm w-auto bg-dark text-light border-0" placeholder="Filtrer..." data-table-filter="#tbl-restaurants">
        </div>
        <div class="table-responsive">
          @if($restaurants->isEmpty())
            <div class="py-4 text-center text-secondary">
              <i class="bi bi-shop fs-3 d-block mb-2"></i>
              <div>Aucun restaurant</div>
            </div>
          @else
            <table id="tbl-restaurants" class="table table-hover align-middle mb-0 sticky text-light">
              <thead>
                <tr>
                  <th><i class="bi bi-card-text"></i> Nom</th>
                  <th><i class="bi bi-geo"></i> Ville</th>
                  <th><i class="bi bi-power"></i> Actif</th>
                  <th class="text-end"><i class="bi bi-gear"></i> Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($restaurants as $r)
                  <tr>
                    <td>{{ $r->name }}</td>
                    <td class="text-light">{{ $r->city }}</td>
                    <td>
                      @if($r->is_active)
                        <span class="badge bg-success">Oui</span>
                      @else
                        <span class="badge bg-secondary">Non</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <form method="POST" action="{{ route('admin.moderation.restaurant.toggle', $r) }}" class="d-inline" data-bs-toggle="tooltip" title="Activer/Désactiver">
                        @csrf
                        <button class="btn btn-sm btn-outline-light" aria-label="Activer/Désactiver"><i class="bi bi-toggle2-on"></i></button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="card-title text-light mb-0"><i class="bi bi-egg-fried"></i> Plats récents</h6>
          <input type="text" class="form-control form-control-sm w-auto bg-dark text-light border-0" placeholder="Filtrer..." data-table-filter="#tbl-dishes">
        </div>
        <div class="table-responsive">
          @if($dishes->isEmpty())
            <div class="py-4 text-center text-secondary">
              <i class="bi bi-egg-fried fs-3 d-block mb-2"></i>
              <div>Aucun plat</div>
            </div>
          @else
            <table id="tbl-dishes" class="table table-hover align-middle mb-0 sticky text-light">
              <thead>
                <tr>
                  <th><i class="bi bi-card-text"></i> Nom</th>
                  <th><i class="bi bi-shop"></i> Restaurant</th>
                  <th><i class="bi bi-power"></i> Disponible</th>
                  <th class="text-end"><i class="bi bi-gear"></i> Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($dishes as $d)
                  <tr>
                    <td>{{ $d->name }}</td>
                    <td class="text-light">{{ $d->restaurant?->name ?? '—' }}</td>
                    <td>
                      @if($d->is_available)
                        <span class="badge bg-success">Oui</span>
                      @else
                        <span class="badge bg-secondary">Non</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <form method="POST" action="{{ route('admin.moderation.dish.toggle', $d) }}" class="d-inline" data-bs-toggle="tooltip" title="Rendre disponible/indisponible">
                        @csrf
                        <button class="btn btn-sm btn-outline-light" aria-label="Rendre disponible/indisponible"><i class="bi bi-toggle2-on"></i></button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="card-title text-light mb-0"><i class="bi bi-calendar-event"></i> Événements récents</h6>
          <input type="text" class="form-control form-control-sm w-auto bg-dark text-light border-0" placeholder="Filtrer..." data-table-filter="#tbl-mevents">
        </div>
        <div class="table-responsive">
          @if($events->isEmpty())
            <div class="py-4 text-center text-secondary">
              <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
              <div>Aucun événement</div>
            </div>
          @else
            <table id="tbl-mevents" class="table table-hover align-middle mb-0 sticky text-light">
              <thead>
                <tr>
                  <th><i class="bi bi-card-text"></i> Nom</th>
                  <th><i class="bi bi-geo"></i> Lieu</th>
                  <th><i class="bi bi-clock-history"></i> Période</th>
                </tr>
              </thead>
              <tbody>
                @foreach($events as $ev)
                  <tr>
                    <td>{{ $ev->name ?? '—' }}</td>
                    <td class="text-light">{{ $ev->location ?? '—' }}</td>
                    <td class="text-light">{{ $ev->start_date ?? '—' }} → {{ $ev->end_date ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="card-title text-light mb-0"><i class="bi bi-building"></i> Hôtels récents</h6>
          <input type="text" class="form-control form-control-sm w-auto bg-dark text-light border-0" placeholder="Filtrer..." data-table-filter="#tbl-hotels">
        </div>
        <div class="table-responsive">
          @if($hotels->isEmpty())
            <div class="py-4 text-center text-secondary">
              <i class="bi bi-building fs-3 d-block mb-2"></i>
              <div>Aucun hôtel</div>
            </div>
          @else
            <table id="tbl-hotels" class="table table-hover align-middle mb-0 sticky text-light">
              <thead>
                <tr>
                  <th><i class="bi bi-card-text"></i> Nom</th>
                  <th><i class="bi bi-geo"></i> Ville</th>
                </tr>
              </thead>
              <tbody>
                @foreach($hotels as $h)
                  <tr>
                    <td>{{ $h->name }}</td>
                    <td class="text-light">{{ $h->city ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
@push('styles')
<style>
  .table.sticky thead th { position: sticky; top: 0; z-index: 5; background: rgba(18,24,38,.9); backdrop-filter: blur(4px); color: #fff; }
</style>
@endpush
@push('scripts')
<script>
  (function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (el) { new bootstrap.Tooltip(el) })
    document.querySelectorAll('[data-table-filter]').forEach(function(input){
      const sel = input.getAttribute('data-table-filter');
      const table = document.querySelector(sel);
      if(!table) return;
      input.addEventListener('input', function(){
        const q = this.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(function(tr){
          const txt = tr.textContent.toLowerCase();
          tr.style.display = txt.indexOf(q) !== -1 ? '' : 'none';
        });
      });
    });
  })();
</script>
@endpush
