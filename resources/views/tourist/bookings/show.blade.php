@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item"><a href="{{ route('tourist.bookings.index') }}">Mes réservations</a></li>
      <li class="breadcrumb-item active" aria-current="page">Détail</li>
    </ol>
  </nav>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="panel-cream rounded-20 p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h1 class="h4 mb-0">Détail de la réservation</h1>
        <div class="text-muted small">Réf. {{ $booking->reference ?? substr($booking->id,0,8) }}</div>
      </div>
      <div><span class="badge text-bg-light border text-uppercase">{{ $booking->status }}</span></div>
    </div>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="panel-cream rounded-20 p-3 h-100">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="text-muted small">Hôtel</div>
              <div class="fw-semibold">{{ $booking->room?->hotel?->name }}</div>
            </div>
            <div class="col-md-6">
              <div class="text-muted small">Chambre</div>
              <div>{{ $booking->room?->name }}</div>
            </div>
            <div class="col-md-6">
              <div class="text-muted small">Dates</div>
              <div>{{ $booking->start_date }} → {{ $booking->end_date }}</div>
            </div>
            <div class="col-md-6">
              <div class="text-muted small">Montant</div>
              <div class="fw-semibold">{{ number_format($booking->total_price, 0, ',', ' ') }} CFA</div>
            </div>
          </div>

          @if(in_array($booking->status, ['pending','confirmed']))
            <form method="POST" action="{{ route('tourist.bookings.cancel', $booking) }}" class="mt-4" onsubmit="return confirm('Annuler cette réservation ?');">
              @csrf
              <button class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i> Annuler la réservation</button>
            </form>
          @endif

          <div class="mt-3 d-flex flex-wrap gap-2">
            <button id="js-add-ics" class="btn btn-primary"><i class="bi bi-calendar-plus"></i> Ajouter au calendrier</button>
            <a href="{{ route('tourist.bookings.index') }}" class="btn btn-light">Retour</a>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="panel-cream rounded-20 position-sticky" style="top: 100px;">
          <div class="px-3 py-2 fw-semibold">Récapitulatif</div>
          <div class="p-3">
            <div class="small text-muted mb-1">Hôtel</div>
            <div class="mb-2">{{ $booking->room?->hotel?->name }}</div>
            <div class="small text-muted mb-1">Chambre</div>
            <div class="mb-2">{{ $booking->room?->name }}</div>
            <div class="small text-muted mb-1">Séjour</div>
            <div class="mb-2">{{ $booking->start_date }} → {{ $booking->end_date }}</div>
            <div class="small text-muted mb-1">Montant</div>
            <div class="fw-semibold">{{ number_format($booking->total_price, 0, ',', ' ') }} CFA</div>
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
    const btn = document.getElementById('js-add-ics');
    if(!btn) return;
    function toYYYYMMDD(dstr){
      try {
        const d = new Date(dstr);
        if(!isNaN(d)){
          const y=d.getFullYear();
          const m=String(d.getMonth()+1).padStart(2,'0');
          const da=String(d.getDate()).padStart(2,'0');
          return `${y}${m}${da}`;
        }
      } catch(e){}
      return String(dstr||'').replaceAll('-','');
    }
    btn.addEventListener('click', function(){
      const hotel = @json($booking->room?->hotel?->name);
      const room = @json($booking->room?->name);
      const title = `${hotel}${room? ' — '+room : ''}`;
      const dtStart = toYYYYMMDD(@json($booking->start_date));
      const dtEnd = toYYYYMMDD(@json($booking->end_date));
      const now = new Date();
      const stamp = now.toISOString().replace(/[-:]/g,'').split('.')[0] + 'Z';
      const uid = `hotel-booking-{{ $booking->id }}@discoverbf`;
      const ics = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Discover BF//Hotel Booking//FR',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        `UID:${uid}`,
        `DTSTAMP:${stamp}`,
        `DTSTART;VALUE=DATE:${dtStart}`,
        `DTEND;VALUE=DATE:${dtEnd}`,
        `SUMMARY:${title.replace(/\n/g,' ')}`,
        `DESCRIPTION:Séjour réservé — Réf. {{ addslashes($booking->reference ?? substr($booking->id,0,8)) }}`,
        'END:VEVENT',
        'END:VCALENDAR'
      ].join('\r\n');
      const blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `${hotel ? hotel.replace(/[^a-z0-9-_]+/gi,'-').toLowerCase() : 'reservation'}.ics`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      setTimeout(()=>URL.revokeObjectURL(url), 1000);
    });
  });
</script>
@endpush
