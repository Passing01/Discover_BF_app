@extends('layouts.tourist')

@push('styles')
<style>
  .panel-cream{background:#f9f1e7;border:1px solid #f0e3d7;}
  .panel-cream .panel-inner{background:#fff7ef;border:1px solid #ead6c5;}
  .soft-card{border:1px solid #efdfd2;border-radius:16px;}
  .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .6rem;border-radius:999px;border:1px solid #e5d3c2;background:#fff; font-size:.85rem;}
  .btn-orange{background:#e85b3a;border-color:#e85b3a;color:#fff;}
  .btn-orange:hover{background:#d04f32;border-color:#d04f32;color:#fff;}
  .btn-cream{background:#f3e5d8;border-color:#e7d3c1;color:#5c4536;}
  .btn-cream:hover{background:#ecd8c7;border-color:#e0c8b3;color:#4a392e;}
  .section-title{font-weight:700;color:#5c4536;}
  .muted{color:#7a6a5e;}
  .rounded-20{border-radius:20px;}
  .header-actions .btn{border-radius:16px;}
  .shadow-soft{box-shadow:0 10px 24px rgba(0,0,0,.06);}  
</style>
@endpush

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-soft">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold muted">Itinéraire généré — <span class="text-dark">{{ $originHotel ?? 'Depuis votre sélection' }}</span></div>
      <div class="header-actions d-flex gap-2">
        <a href="{{ route('assistant.export') }}" class="btn btn-cream btn-sm"><i class="bi bi-download"></i> Exporter</a>
        <a href="{{ route('tourist.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i> Fermer</a>
      </div>
    </div>
    <div class="small muted mb-3">Nous avons préparé un plan d'une journée avec hébergement, nature et transport. Ajustez ou réservez directement.</div>
    <div class="panel-inner rounded-20 p-3">
      <div class="d-flex flex-wrap gap-2 mb-3">
        <span class="chip"><i class="bi bi-building me-1"></i> Hôtel</span>
        <span class="chip"><i class="bi bi-tree me-1"></i> Nature</span>
        <span class="chip"><i class="bi bi-taxi-front me-1"></i> Taxi</span>
        <span class="chip"><i class="bi bi-geo-alt me-1"></i> {{ $city ?? 'Ouagadougou' }}</span>
      </div>
      <div class="row g-3">
        <div class="col-lg-8 vstack gap-3">
          <div class="d-flex justify-content-between align-items-center">
            <div class="section-title">Votre journée en un clin d'œil</div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
      <div class="small muted">Actions suivantes: Réserver hôtel · Réserver taxi · Ajouter activité nature</div>
      <div class="d-flex gap-2">
        <a href="{{ route('assistant.preview') }}" class="btn btn-cream btn-sm"><i class="bi bi-eye"></i> Prévisualiser la journée</a>
        <a href="{{ route('tourist.itinerary') }}" class="btn btn-orange btn-sm"><i class="bi bi-bookmark-heart"></i> Enregistrer l'itinéraire</a>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8 vstack gap-4">
      <!-- Votre journée (soft list) -->
      <div class="soft-card p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold text-dark">Votre journée en un clin d'œil</div>
        </div>
        <div class="vstack gap-2">
          @if(isset($sites) && count($sites))
            @php($firstSite = $sites[0])
            <div class="border rounded-20 p-2 d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('assets/img/portfolio/portfolio-1.jpg') }}" class="rounded" style="width:52px;height:52px;object-fit:cover" alt="site">
                <div>
                  <div class="fw-semibold">{{ $firstSite['name'] ?? 'Activité' }}</div>
                  <div class="small text-muted">{{ $firstSite['city'] ?? '—' }} · Suggestion</div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <a href="{{ route('assistant.options') }}" class="btn btn-cream btn-sm">Voir options</a>
                <a href="{{ route('assistant.add', ['type'=>'site','id'=>$firstSite['id'] ?? null,'label'=>$firstSite['name'] ?? 'Site']) }}" class="btn btn-orange btn-sm">Ajouter</a>
              </div>
            </div>
          @endif
          @if(isset($events) && count($events))
            @php($firstEvent = $events[0])
            <div class="border rounded-20 p-2 d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('assets/img/portfolio/portfolio-5.jpg') }}" class="rounded" style="width:52px;height:52px;object-fit:cover" alt="event">
                <div>
                  <div class="fw-semibold">{{ $firstEvent->title }}</div>
                  <div class="small text-muted">{{ $firstEvent->city ?? '—' }} · {{ \Illuminate\Support\Carbon::parse($firstEvent->starts_at)->format('H:i') }}</div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <a href="{{ route('events.show', $firstEvent) }}" class="btn btn-cream btn-sm">Détails</a>
                <a href="{{ route('events.show', $firstEvent) }}" class="btn btn-orange btn-sm">Réserver</a>
              </div>
            </div>
          @endif
          @if(isset($taxis) && count($taxis))
            @php($tx = $taxis[0])
            <div class="border rounded-20 p-2 d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <span class="badge text-bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:52px;height:52px"><i class="bi bi-taxi-front fs-5"></i></span>
                <div>
                  <div class="fw-semibold">{{ $tx->name ?? 'Taxi' }}</div>
                  <div class="small text-muted">Prise en charge: {{ $city ?? 'Hôtel' }} · Est. {{ isset($tx->base_price) ? number_format($tx->base_price,0).' FCFA' : 'Tarif variable' }}</div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <a href="{{ route('transport.taxi.index') }}" class="btn btn-cream btn-sm">Planifier</a>
                <a href="{{ route('transport.taxi.index') }}" class="btn btn-orange btn-sm">Réserver</a>
              </div>
            </div>
          @endif
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Vols suggérés (arrivée au Burkina Faso)</h5>
          @forelse($flights as $f)
            <div class="border rounded p-3 mb-2">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <div><strong>{{ $f->origin->city }}</strong> → <strong>{{ $f->destination->city }}</strong></div>
                  <div class="text-muted small">{{ $f->airline ?? '—' }} · {{ $f->flight_number ?? '—' }}</div>
                  <div class="small">Départ: {{ $f->departure_time }} | Arrivée: {{ $f->arrival_time }}</div>
                </div>
                <div class="text-end">
                  <div class="fw-bold">{{ number_format($f->base_price, 0) }} FCFA</div>
                  <a href="{{ route('air.flights.show', $f) }}" class="btn btn-sm btn-outline-primary mt-1">Voir</a>
                  <a href="{{ route('air.flights.book', $f) }}" class="btn btn-sm btn-primary mt-1">Réserver</a>
                </div>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">Aucun vol trouvé près de votre date d'arrivée. Essayez d'ajuster la date ou l'origine.</p>
          @endforelse
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Idées d'activités et sites à visiter</h5>
          @if(count($sites))
            <ul class="mb-0">
              @foreach($sites as $s)
                <li>{{ $s['city'] }} — {{ $s['name'] }}</li>
              @endforeach
            </ul>
          @else
            <p class="text-muted mb-0">Aucune suggestion spécifique pour vos centres d'intérêt, explorez nos guides locaux.</p>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Évènements pendant votre séjour</h5>
          @if(isset($events) && count($events))
            <ul class="mb-0">
              @foreach($events as $e)
                <li>
                  <strong>{{ $e->title }}</strong> — {{ $e->city }}
                  <div class="small text-muted">{{ $e->starts_at }} @if($e->venue) · {{ $e->venue }} @endif</div>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-muted mb-0">Aucun évènement trouvé sur vos dates. Essayez d'ajuster la période.</p>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Trajets bus pendant le séjour</h5>
          @forelse($busTrips as $t)
            <div class="border rounded p-3 mb-2">
              <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                  <div><strong>{{ $t->origin_city ?? 'Départ' }}</strong> → <strong>{{ $t->destination_city ?? 'Arrivée' }}</strong></div>
                  <div class="small">Départ: {{ $t->departure_time }}</div>
                </div>
                <div class="text-end">
                  @if(!empty($t->price))
                    <div class="fw-bold">{{ number_format($t->price, 0) }} FCFA</div>
                  @endif
                  <a href="{{ route('transport.bus.index') }}" class="btn btn-sm btn-outline-primary mt-1">Voir les bus</a>
                </div>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">Aucun trajet bus suggéré pour vos dates.</p>
          @endforelse
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Transfert aéroport (Taxis)</h5>
          @forelse($taxis as $tx)
            <div class="border rounded p-3 mb-2 d-flex justify-content-between">
              <div>
                <div class="fw-bold">{{ $tx->name ?? 'Taxi' }}</div>
                <div class="small text-muted">Capacité: {{ $tx->capacity ?? '—' }}</div>
              </div>
              <div class="text-end">
                @if(!empty($tx->base_price))
                  <div class="fw-bold">{{ number_format($tx->base_price, 0) }} FCFA</div>
                @endif
                <a href="{{ route('transport.taxi.index') }}" class="btn btn-sm btn-outline-primary mt-1">Voir les taxis</a>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">Aucun taxi à suggérer pour le moment.</p>
          @endforelse
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Itinéraire jour par jour</h5>
          @if(isset($itinerary) && count($itinerary))
            <div class="accordion" id="itinerary">
              @foreach($itinerary as $day => $items)
                <div class="accordion-item">
                  <h2 class="accordion-header" id="h-{{ $loop->index }}">
                    <button class="accordion-button @if($loop->index>0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#c-{{ $loop->index }}">
                      Jour {{ $loop->iteration }} — {{ $day }}
                    </button>
                  </h2>
                  <div id="c-{{ $loop->index }}" class="accordion-collapse collapse @if($loop->first) show @endif">
                    <div class="accordion-body">
                      @if(count($items))
                        <ul class="mb-0">
                          @foreach($items as $it)
                            <li>
                              <span class="badge bg-{{ $it['type']==='event' ? 'warning' : 'success' }} text-dark">{{ ucfirst($it['type']) }}</span>
                              {{ $it['title'] }} — {{ $it['city'] }}
                              @if(!empty($it['time'])) <span class="small text-muted">({{ $it['time'] }})</span> @endif
                            </li>
                          @endforeach
                        </ul>
                      @else
                        <p class="text-muted mb-0">Aucune activité prévue.</p>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-muted mb-0">Itinéraire non disponible.</p>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <!-- Ajuster et finaliser -->
      <div class="soft-card p-3 bg-white mb-3">
        <div class="fw-semibold mb-2">Ajuster et finaliser</div>
        <div class="vstack gap-2">
          <div class="border rounded-20 p-2 d-flex align-items-center justify-content-between">
            <div><i class="bi bi-calendar3 me-2"></i>Dates</div>
            <div class="text-muted small">{{ $start->toDateString() }} → {{ $end->toDateString() }}</div>
          </div>
          <div class="border rounded-20 p-2 d-flex align-items-center justify-content-between">
            <div><i class="bi bi-people me-2"></i>Voyageurs</div>
            <div class="text-muted small">2 adultes</div>
          </div>
          <div class="border rounded-20 p-2 d-flex align-items-center justify-content-between">
            <div><i class="bi bi-stars me-2"></i>Thème</div>
            <div class="text-muted small">{{ ($input['theme'] ?? 'Nature + Ville') }}</div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <a href="{{ route('assistant.preview') }}" class="btn btn-cream btn-sm"><i class="bi bi-eye"></i> Prévisualiser</a>
          <a href="{{ route('tourist.itinerary') }}" class="btn btn-orange btn-sm"><i class="bi bi-bookmark-heart"></i> Enregistrer</a>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Répartition Budgétaire</h5>
          <ul class="mb-3">
            <li>Transport: <strong>{{ number_format($budgetSplit['transport'], 0) }} FCFA</strong></li>
            <li>Hébergement: <strong>{{ number_format($budgetSplit['hebergement'], 0) }} FCFA</strong></li>
            <li>Activités: <strong>{{ number_format($budgetSplit['activites'], 0) }} FCFA</strong></li>
            <li>Restauration: <strong>{{ number_format($budgetSplit['restauration'], 0) }} FCFA</strong></li>
          </ul>
          <a href="{{ route('assistant.index') }}" class="btn btn-outline-secondary w-100">Modifier les critères</a>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h6 class="card-title">Conseils</h6>
          <ul class="small mb-0">
            <li>Réservez tôt pour de meilleurs tarifs.</li>
            <li>Prévoyez du temps pour les déplacements interurbains.</li>
            <li>Consultez l'agenda culturel pour les événements.</li>
          </ul>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h6 class="card-title mb-2">Questions à l'IA locale</h6>
          <div class="d-flex flex-wrap gap-2 mb-2">
            <button class="btn btn-sm btn-outline-secondary ai-quick" data-prompt="Optimise mon itinéraire pour réduire les coûts de transport.">Optimiser budget</button>
            <button class="btn btn-sm btn-outline-secondary ai-quick" data-prompt="Propose un itinéraire adapté à une famille avec enfants.">Famille</button>
            <button class="btn btn-sm btn-outline-secondary ai-quick" data-prompt="Ajoute plus d'activités culturelles et patrimoine.">+ Culture</button>
            <button class="btn btn-sm btn-outline-secondary ai-quick" data-prompt="Ajoute plus d'activités nature et plein air.">+ Nature</button>
          </div>
          <textarea id="ai-prompt" class="form-control mb-2" rows="3" placeholder="Ex: Peux-tu optimiser mon itinéraire pour respecter mon budget ?"></textarea>
          <div class="d-flex align-items-center gap-2">
            <button id="ai-submit" class="btn btn-primary flex-grow-1">Demander</button>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="ai-stream">
              <label class="form-check-label small" for="ai-stream">Réponse en direct</label>
            </div>
          </div>
          <div id="ai-result" class="mt-3 small"></div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h6 class="card-title mb-2">Carte interactive</h6>
          <div id="assistant-map" style="height: 320px;" class="rounded overflow-hidden border"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var points = @json($mapPoints ?? []);
      if (!points.length) return;
      var map = L.map('assistant-map');
      var bounds = [];
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);
      points.forEach(function(p) {
        if (typeof p.lat !== 'number' || typeof p.lng !== 'number') return;
        var m = L.marker([p.lat, p.lng]).addTo(map);
        m.bindPopup((p.type==='event'?'[Évènement] ':'[Site] ') + p.label + ' — ' + (p.city||''));
        bounds.push([p.lat, p.lng]);
      });
      if (bounds.length) map.fitBounds(bounds, { padding: [20,20] });

      // Local AI ask
      var btn = document.getElementById('ai-submit');
      if (btn) {
        btn.addEventListener('click', async function() {
          var prompt = document.getElementById('ai-prompt').value.trim();
          var out = document.getElementById('ai-result');
          if (!prompt) { out.innerHTML = '<span class="text-muted">Veuillez saisir une question.</span>'; return; }
          var stream = document.getElementById('ai-stream').checked;
          var context = {
            dates: '{{ $start->toDateString() }} → {{ $end->toDateString() }}',
            budget: '{{ $input['budget'] ?? '' }}',
            cities: @json(array_values(array_unique(array_merge(
              ($sites??collect())->pluck('city')->filter()->values()->all(),
              ($events??collect())->pluck('city')->filter()->values()->all()
            )))),
            sites: @json(($sites??collect())->pluck('name')->values()->all()),
            events: @json(($events??collect())->pluck('title')->values()->all()),
          };

          if (stream && window.EventSource) {
            out.innerHTML = '';
            const params = new URLSearchParams();
            params.set('prompt', prompt);
            params.set('context', JSON.stringify(context));
            if (window.__ai_es) { try { window.__ai_es.close(); } catch(_){} }
            const es = new EventSource('{{ route('assistant.ai.stream') }}' + '?' + params.toString());
            window.__ai_es = es;
            es.onmessage = function(ev) {
              // Append streamed tokens
              var text = ev.data || '';
              if (text) {
                out.innerHTML += text.replace(/</g,'&lt;');
              }
            };
            es.addEventListener('done', function(){ es.close(); });
            es.onerror = function(){
              es.close();
              if (!out.innerHTML.trim()) {
                out.innerHTML = '<span class="text-danger">Erreur de flux (SSE). Vérifiez que Ollama tourne et réessayez.</span>';
              }
            };
          } else {
            out.innerHTML = '\u231B Génération en cours...';
            try {
              const res = await fetch('{{ route('assistant.ai') }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ prompt, context })
              });
              const raw = await res.text();
              let data;
              try { data = JSON.parse(raw); } catch(_) { data = null; }
              if (!res.ok) {
                const msg = (data && (data.error || data.message)) || raw.slice(0,300);
                throw new Error(msg || 'Erreur HTTP '+res.status);
              }
              if (!data || !data.ok) {
                throw new Error((data && (data.error || data.message)) || 'Réponse invalide');
              }
              out.innerHTML = (data.text || '')
                .split('\n')
                .map(l => l.trim().length ? '<div>'+l.replace(/</g,'&lt;')+'</div>' : '<br>')
                .join('');
            } catch (e) {
              out.innerHTML = '<span class="text-danger">'+(e.message||'Erreur')+'</span>';
            }
          }
        });
      }

      // Quick prompt buttons
      document.querySelectorAll('.ai-quick').forEach(function(b){
        b.addEventListener('click', function(){
          var ta = document.getElementById('ai-prompt');
          ta.value = this.getAttribute('data-prompt') || '';
          ta.focus();
        });
      });
    });
  </script>
@endpush
