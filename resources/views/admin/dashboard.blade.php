@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title"><i class="bi bi-speedometer2"></i> Dashboard</h4>
  </div>
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="stat">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-secondary">Utilisateurs</div>
            <div class="h3 m-0">{{ $metrics['users']['total'] }}</div>
          </div>
          <i class="bi bi-people fs-2"></i>
        </div>
        <div class="small text-secondary mt-1">Actifs: {{ $metrics['users']['active'] }} • Admins: {{ $metrics['users']['admins'] }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-secondary">Événements</div>
            <div class="h3 m-0">{{ $metrics['events']['total'] }}</div>
          </div>
          <i class="bi bi-calendar-event fs-2"></i>
        </div>
        <div class="small text-secondary mt-1">Réservations: {{ $metrics['events']['bookings'] }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat">
        <div class="text-secondary">Revenus (confirmés)</div>
        <div class="h3 m-0">{{ number_format($metrics['commerce']['payments_total'], 0, ',', ' ') }} CFA</div>
        <div class="small text-secondary mt-1">Commandes: {{ $metrics['commerce']['dish_orders'] }} • Réserv. resto: {{ $metrics['commerce']['restaurant_reservations'] }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat">
        <div class="text-secondary">Transport</div>
        <div class="h3 m-0">{{ $metrics['transport']['flight_bookings'] + $metrics['transport']['bus_bookings'] + $metrics['transport']['rides'] }}</div>
        <div class="small text-secondary mt-1">Vols: {{ $metrics['transport']['flight_bookings'] }} • Bus: {{ $metrics['transport']['bus_bookings'] }} • Taxis: {{ $metrics['transport']['rides'] }}</div>
      </div>
    </div>
  </div>

  <!-- Charts: Roles distribution & Entities volume -->
  <div class="row g-3 mb-3">
    <div class="col-lg-6">
      <div class="bg-secondary rounded p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="card-title"><i class="bi bi-graph-up"></i> Répartition des rôles</h6>
        </div>
        <canvas id="rolesChart" height="200"></canvas>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="bg-secondary rounded p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="card-title"><i class="bi bi-bar-chart"></i> Compteurs entités</h6>
        </div>
        <canvas id="entitiesChart" height="200"></canvas>
      </div>
    </div>
  </div>

  <!-- Time-series: Payments & Bookings with period filter -->
  <div class="bg-secondary rounded p-4 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="card-title m-0"><i class="bi bi-activity"></i> Évolution paiements & réservations</h6>
      <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
        <label for="period" class="small text-light me-2">Période</label>
        <select id="period" name="period" class="form-select form-select-sm bg-dark text-light" onchange="this.form.submit()">
          <option value="7" {{ ($timeseries['period'] ?? 30) == 7 ? 'selected' : '' }}>7 jours</option>
          <option value="30" {{ ($timeseries['period'] ?? 30) == 30 ? 'selected' : '' }}>30 jours</option>
          <option value="90" {{ ($timeseries['period'] ?? 30) == 90 ? 'selected' : '' }}>90 jours</option>
        </select>
      </form>
    </div>
    <canvas id="timeseriesChart" height="110"></canvas>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="card-title"><i class="bi bi-people"></i> Utilisateurs récents</h6>
          <a class="btn btn-sm btn-outline-light" href="{{ route('admin.users') }}"><i class="bi bi-list"></i> Voir tout</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th><i class="bi bi-person-badge"></i> Nom</th>
                <th><i class="bi bi-envelope"></i> Email</th>
                <th><i class="bi bi-person-gear"></i> Rôle</th>
                <th><i class="bi bi-activity"></i> Statut</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentUsers as $u)
                <tr>
                  <td>{{ $u->first_name }} {{ $u->last_name }}</td>
                  <td class="text-secondary">{{ $u->email }}</td>
                  <td><span class="badge bg-secondary">{{ $u->role }}</span></td>
                  <td>
                    @if($u->is_active)
                      <span class="badge bg-success">Actif</span>
                    @else
                      <span class="badge bg-secondary">Inactif</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-secondary">Aucun utilisateur</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="card-title"><i class="bi bi-calendar-event"></i> Événements récents</h6>
          <a class="btn btn-sm btn-outline-light" href="{{ route('admin.events') }}"><i class="bi bi-gear"></i> Gérer</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th><i class="bi bi-card-text"></i> Nom</th>
                <th><i class="bi bi-clock-history"></i> Date</th>
                <th><i class="bi bi-geo"></i> Lieu</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentEvents as $ev)
                <tr>
                  <td>{{ $ev->name ?? '—' }}</td>
                  <td>{{ $ev->date ?? '—' }}</td>
                  <td>{{ $ev->location ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-secondary">Aucun événement</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  @push('scripts')
  <!-- Charts scripts (use template's Chart.js from assets_admin) -->
  <script>
    (function(){
      function initAdminCharts(){
        const rolesLabels = @json($rolesChart['labels'] ?? []);
        const rolesData = @json($rolesChart['data'] ?? []);
        const entitiesLabels = @json($entitiesChart['labels'] ?? []);
        const entitiesData = @json($entitiesChart['data'] ?? []);
        const tsLabels = @json($timeseries['labels'] ?? []);
        const tsPayments = @json($timeseries['payments'] ?? []);
        const tsBookings = @json($timeseries['bookings'] ?? []);

        const rolesCtx = document.getElementById('rolesChart');
        if (rolesCtx && window.Chart) {
          new Chart(rolesCtx, {
          type: 'doughnut',
          data: {
            labels: rolesLabels,
            datasets: [{
              data: rolesData,
                backgroundColor: [
                  'rgba(235, 22, 22, .7)',
                  'rgba(235, 22, 22, .6)',
                  'rgba(235, 22, 22, .5)',
                  'rgba(235, 22, 22, .4)',
                  'rgba(235, 22, 22, .3)',
                  'rgba(235, 22, 22, .2)',
                  'rgba(235, 22, 22, .15)',
                  'rgba(235, 22, 22, .1)'
                ],
              }]
            },
            options: {
            responsive: true,
            legend: { position: 'bottom' }
          }
        });
        }

        const entitiesCtx = document.getElementById('entitiesChart');
        if (entitiesCtx && window.Chart) {
          new Chart(entitiesCtx, {
          type: 'bar',
          data: {
            labels: entitiesLabels,
            datasets: [{
              label: 'Totaux',
              data: entitiesData,
              backgroundColor: 'rgba(235, 22, 22, .7)'
            }]
          },
          options: {
            responsive: true,
            legend: { display: false },
            scales: {
              yAxes: [{ ticks: { beginAtZero: true, callback: function(v){ return Number.isInteger(v)? v : v; } } }]
            }
          }
        });
        }

        const tsCtx = document.getElementById('timeseriesChart');
        if (tsCtx && window.Chart) {
          new Chart(tsCtx, {
          type: 'line',
          data: {
            labels: tsLabels,
            datasets: [
              {
                label: 'Paiements (CFA)',
                data: tsPayments,
                borderColor: 'rgba(235, 22, 22, .7)',
                backgroundColor: 'rgba(235, 22, 22, .3)',
                tension: 0.25,
                yAxisID: 'y1',
                fill: true
              },
              {
                label: 'Réservations (nb)',
                data: tsBookings,
                borderColor: 'rgba(235, 22, 22, .5)',
                backgroundColor: 'rgba(235, 22, 22, .2)',
                tension: 0.25,
                yAxisID: 'y2',
                fill: true
              }
            ]
          },
          options: {
            responsive: true,
            legend: { position: 'bottom' },
            tooltips: { mode: 'index', intersect: false },
            hover: { mode: 'nearest', intersect: true },
            scales: {
              yAxes: [
                { id: 'y1', position: 'left', ticks: { beginAtZero: true } },
                { id: 'y2', position: 'right', ticks: { beginAtZero: true }, gridLines: { drawOnChartArea: false } }
              ]
            }
          }
        });
        }
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminCharts);
      } else {
        initAdminCharts();
      }
    })();
  </script>
  @endpush
@endsection
