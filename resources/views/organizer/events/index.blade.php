@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <h1 class="mb-0 text-orange">Mes évènements</h1>
    <div class="ms-auto d-flex gap-2">
      <a href="{{ route('organizer.events.wizard.start') }}" class="btn btn-cream"><i class="bi bi-magic me-1"></i> Créer via Wizard</a>
      <a href="{{ route('organizer.events.create') }}" class="btn btn-orange"><i class="bi bi-plus-lg me-1"></i> Nouvel évènement</a>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if(session('ticket_link'))
    <div class="alert alert-info">
      Ticket de test créé: <a class="alert-link" href="{{ session('ticket_link') }}" target="_blank" rel="noopener">ouvrir l'aperçu</a>
    </div>
  @endif

  @if($events->count())
    <div class="row g-3">
      @foreach($events as $event)
        <div class="col-12 col-md-6 col-lg-4">
          <div class="panel-cream rounded-20 h-100 d-flex flex-column">
            @if(!empty($event->image_path))
              <div class="ratio ratio-16x9" style="background:#f2e9e1;">
                <div style="background-image:url('{{ asset('storage/'.$event->image_path) }}');background-size:cover;background-position:center;border-bottom:1px solid rgba(0,0,0,.05);width:100%;height:100%;"></div>
              </div>
            @endif
            <div class="p-3 p-md-4 d-flex flex-column flex-grow-1" style="min-height: 140px;">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <h5 class="mb-0">{{ $event->name }}</h5>
                <small class="text-muted">{{ $event->start_date }} → {{ $event->end_date }}</small>
              </div>
              <div class="small text-muted mb-2 d-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-soft"><i class="bi bi-geo-alt me-1"></i>{{ $event->location }}</span>
                @if(!is_null($event->ticket_price))
                  <span class="badge badge-soft">{{ number_format($event->ticket_price,0,',',' ') }} XOF</span>
                @endif
                @if(!empty($event->category))
                  <span class="badge badge-soft">{{ $event->category }}</span>
                @endif
              </div>
              @if(!empty($event->description))
                <p class="mt-1 mb-3">{{ Str::limit($event->description, 120) }}</p>
              @endif
              <div class="mt-auto pt-2 d-flex gap-2 flex-wrap">
                <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-orange btn-sm"><i class="bi bi-pencil me-1"></i> Modifier</a>
                <a href="{{ route('events.show', $event) }}" class="btn btn-cream btn-sm" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i> Voir côté public</a>
                <form action="{{ route('organizer.events.destroy', $event) }}" method="post" onsubmit="return confirm('Supprimer cet évènement ? Cette action est irréversible.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i> Supprimer</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="alert alert-info rounded-20">Aucun évènement.</div>
  @endif

  <div class="mt-3">{{ $events->links() }}</div>
</div>
@endsection
