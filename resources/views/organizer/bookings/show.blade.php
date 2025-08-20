@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <h1 class="h4 mb-0 text-orange">Détails de la vente</h1>
    <div class="ms-auto">
      <a href="{{ route('organizer.sales.index') }}" class="btn btn-cream"><i class="bi bi-arrow-left me-1"></i> Retour aux ventes</a>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="panel-cream rounded-20 h-100">
        <div class="p-3 p-md-4">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <div class="text-muted">Référence</div>
              <div class="fw-semibold small">{{ $booking->id }}</div>
            </div>
            <div>
              @php($status = $booking->status ?? 'paid')
              <span class="badge @if($status==='paid') bg-success @elseif($status==='pending') bg-warning text-dark @else bg-secondary @endif">{{ ucfirst($status) }}</span>
            </div>
          </div>

          <div class="mb-3">
            <div class="text-muted">Acheteur</div>
            <div class="fw-semibold">{{ $booking->buyer_name ?? $booking->user?->name }}</div>
            <div class="small text-muted">{{ $booking->buyer_email ?? $booking->user?->email }}</div>
          </div>

          <div class="mb-3">
            <div class="text-muted">Montant total</div>
            <div class="fs-5 fw-semibold">{{ number_format($booking->total_amount ?? 0, 0, ',', ' ') }} XOF</div>
          </div>

          <div class="mb-2 d-flex gap-3 flex-wrap">
            <div class="text-muted">Créée le</div>
            <div>{{ $booking->created_at?->format('d/m/Y H:i') }}</div>
          </div>

          <hr>

          <div class="mb-2 fw-semibold">Billets émis</div>
          @if($booking->tickets && $booking->tickets->count())
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Émis le</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($booking->tickets as $idx => $ticket)
                    <tr>
                      <td>{{ $idx + 1 }}</td>
                      <td>{{ $ticket->type?->name ?? 'Standard' }}</td>
                      <td>
                        @php($tStatus = $ticket->status ?? 'issued')
                        <span class="badge @if($tStatus==='issued') bg-primary @elseif($tStatus==='validated') bg-success @else bg-secondary @endif">{{ ucfirst($tStatus) }}</span>
                      </td>
                      <td>{{ $ticket->issued_at?->format('d/m/Y H:i') }}</td>
                      <td class="text-end">
                        @if(!empty($ticket->uuid))
                          <a href="{{ route('tickets.show.uuid', $ticket->uuid) }}" target="_blank" class="btn btn-sm btn-cream"><i class="bi bi-ticket-perforated me-1"></i> Ouvrir</a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="alert alert-secondary">Aucun billet émis.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="panel-cream rounded-20">
        <div class="p-3 p-md-4">
          <div class="d-flex align-items-center mb-2">
            <div>
              <div class="text-muted">Évènement</div>
              <div class="fw-semibold">{{ $booking->event?->name }}</div>
              <div class="small text-muted">{{ $booking->event?->location }}</div>
              <div class="small text-muted">{{ $booking->event?->start_date }} → {{ $booking->event?->end_date }}</div>
            </div>
            <div class="ms-auto">
              @if(!empty($booking->event?->image_path))
                <img src="{{ asset('storage/'.$booking->event->image_path) }}" alt="Poster" class="rounded" style="width: 84px; height: 84px; object-fit: cover;">
              @endif
            </div>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('events.show', $booking->event) }}" class="btn btn-cream btn-sm" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i> Voir côté public</a>
            <a href="{{ route('organizer.events.edit', $booking->event) }}" class="btn btn-orange btn-sm"><i class="bi bi-pencil me-1"></i> Éditer l'évènement</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
