@extends('layouts.tourist')
@php use Illuminate\Support\Str; @endphp

@push('styles')
<style>
  .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  }
  .card-header {
    background-color: #fff;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    font-weight: 600;
    padding: 1.25rem 1.5rem;
    border-radius: 12px 12px 0 0 !important;
  }
  .stat-card {
    border-left: 4px solid #4361ee;
  }
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
  }
  .stat-label {
    color: #718096;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .message-card {
    border-left: 4px solid #4f46e5;
  }
  .message-time {
    font-size: 0.75rem;
    color: #6b7280;
  }
  .btn-action {
    padding: 0.35rem 0.75rem;
    font-size: 0.85rem;
    border-radius: 8px;
  }
</style>
@endpush

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1">Tableau de bord</h1>
      <p class="text-muted mb-0">Bienvenue, {{ auth()->user()->first_name }} ! Voici un aperçu de votre activité.</p>
    </div>
    <div>
      <a href="{{ route('sites.index') }}" class="btn btn-primary">
        <i class="bi bi-compass me-1"></i> Voir les sites
      </a>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Section Disponibilités -->
  @if($guide && ($guide->available_from || $guide->available_to))
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span>Vos disponibilités</span>
        <a href="{{ route('guide.availability.edit') }}" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-pencil-square me-1"></i> Modifier
        </a>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p class="mb-1"><strong>Disponible du :</strong></p>
            <p>{{ $guide->available_from ? $guide->available_from->format('d/m/Y H:i') : 'Non défini' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Jusqu'au :</strong></p>
            <p>{{ $guide->available_to ? $guide->available_to->format('d/m/Y H:i') : 'Non défini' }}</p>
          </div>
        </div>
        @if($guide->availability_note)
          <div class="mt-3">
            <p class="mb-1"><strong>Note :</strong></p>
            <p class="mb-0">{{ $guide->availability_note }}</p>
          </div>
        @endif
      </div>
    </div>
  @endif

  <!-- Stats Row -->
  <div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card h-100">
        <div class="card-body">
          <div class="stat-number">{{ $contacts->count() }}</div>
          <div class="stat-label">Messages reçus</div>
          <div class="mt-2">
            <i class="bi bi-envelope text-primary"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card h-100">
        <div class="card-body">
          <div class="stat-number">{{ $tourBookings->count() + $eventBookings->count() }}</div>
          <div class="stat-label">Réservations</div>
          <div class="mt-2">
            <i class="bi bi-calendar-check text-success"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card h-100">
        <div class="card-body">
          <div class="stat-number">{{ $contacts->where('status', 'new')->count() }}</div>
          <div class="stat-label">Nouveaux messages</div>
          <div class="mt-2">
            <i class="bi bi-bell-fill text-warning"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card h-100">
        <div class="card-body">
          <div class="stat-number">{{ $sitesCount ?? 0 }}</div>
          <div class="stat-label">Sites gérés</div>
          <div class="mt-2">
            <i class="bi bi-geo-alt text-info"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Availability Section -->
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Vos disponibilités</span>
      <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#availabilityForm">
        <i class="bi bi-pencil-square me-1"></i> Modifier
      </button>
    </div>
    <div class="collapse" id="availabilityForm">
      <div class="card-body border-top">
        <form method="POST" action="{{ route('guide.availability.update') }}" class="row g-3">
          @csrf
          <div class="col-md-4">
            <label class="form-label fw-medium">Disponible du</label>
            <input type="date" name="available_from" class="form-control @error('available_from') is-invalid @enderror" required>
            @error('available_from')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">au</label>
            <input type="date" name="available_to" class="form-control @error('available_to') is-invalid @enderror" required>
            @error('available_to')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Spécialité/Note</label>
            <div class="input-group">
              <input type="text" name="note" class="form-control" placeholder="Ex: spécialité danse traditionnelle">
              <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-lg"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card-body py-3">
      <div class="d-flex align-items-center">
        <i class="bi bi-calendar-check text-primary me-2"></i>
        <span>Vos prochaines disponibilités seront affichées ici</span>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Dernières réservations (Tours)</span>
          <a href="#" class="btn btn-sm btn-outline-secondary">Voir tout</a>
        </div>
        <div class="list-group list-group-flush">
          @forelse($tourBookings->take(5) as $booking)
            <div class="list-group-item border-0 py-3">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-medium">Réservation #{{ $booking->id }}</div>
                  <div class="small text-muted">
                    <i class="bi bi-calendar3 me-1"></i> {{ $booking->date?->format('d/m/Y H:i') ?? 'Date non spécifiée' }}
                  </div>
                  <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }} mt-1">
                    {{ $booking->status ?? 'En attente' }}
                  </span>
                </div>
                <a href="#" class="btn btn-sm btn-outline-primary btn-action">
                  <i class="bi bi-eye"></i>
                </a>
              </div>
            </div>
          @empty
            <div class="text-center py-4 text-muted">
              <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
              Aucune réservation de tour
            </div>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Prochains événements</span>
          <a href="#" class="btn btn-sm btn-outline-secondary">Voir tout</a>
        </div>
        <div class="list-group list-group-flush">
          @forelse($eventBookings->take(5) as $booking)
            <div class="list-group-item border-0 py-3">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-medium">Événement #{{ $booking->id }}</div>
                  <div class="small text-muted">
                    <i class="bi bi-calendar3 me-1"></i> {{ $booking->date?->format('d/m/Y H:i') ?? 'Date non spécifiée' }}
                  </div>
                  <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }} mt-1">
                    {{ $booking->status ?? 'En attente' }}
                  </span>
                </div>
                <a href="#" class="btn btn-sm btn-outline-primary btn-action">
                  <i class="bi bi-eye"></i>
                </a>
              </div>
            </div>
          @empty
            <div class="text-center py-4 text-muted">
              <i class="bi bi-calendar-event fs-4 d-block mb-2"></i>
              Aucun événement à venir
            </div>
          @endforelse
        </div>
      </div>
    </div>
  <!-- Messages Section -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <span>Messages récents des touristes</span>
        @if($unreadCount = $contacts->where('status', 'new')->count())
          <span class="badge bg-danger ms-2">{{ $unreadCount }} non lus</span>
        @endif
      </div>
      <div>
        <a href="{{ route('guide.messages.index') }}" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-chat-dots me-1"></i> Voir tous les messages
        </a>
      </div>
    </div>
    <div class="list-group list-group-flush">
      @forelse($contacts->take(4) as $contact)
        <a href="{{ route('guide.messages.index', $contact->id) }}" class="list-group-item list-group-item-action border-0 py-3 message-card {{ $contact->status === 'new' ? 'bg-light' : '' }}">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1 me-3">
              <div class="d-flex justify-content-between align-items-start">
                <div class="fw-medium">
                  {{ $contact->name }}
                  @if($contact->status === 'new')
                    <span class="badge bg-primary ms-2">Nouveau</span>
                  @endif
                </div>
                <div class="message-time">
                  {{ $contact->created_at?->diffForHumans() }}
                </div>
              </div>
              <div class="text-muted small mb-1">{{ $contact->email }}</div>
              <div class="text-truncate">{{ Str::limit($contact->message, 120) }}</div>
            </div>
            @if($contact->phone)
              <div class="ms-2">
                <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); window.location.href='tel:{{ $contact->phone }}'" title="Appeler {{ $contact->phone }}">
                  <i class="bi bi-telephone"></i>
                </button>
              </div>
            @endif
          </div>
        </a>
      @empty
        <div class="text-center py-5 text-muted">
          <i class="bi bi-envelope fs-1 d-block mb-2"></i>
          Aucun message pour le moment
        </div>
      @endforelse
    </div>
    @if($contacts->count() > 4)
      <div class="card-footer text-center">
        <a href="{{ route('guide.messages.index') }}" class="btn btn-link">
          Voir tous les messages <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    @endif
  </div>
  </div>
</div>
@endsection
