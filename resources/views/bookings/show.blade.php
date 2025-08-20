@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Agenda culturel</a></li>
      <li class="breadcrumb-item"><a href="{{ route('events.show', $booking->event) }}">{{ $booking->event->name }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Confirmation</li>
    </ol>
  </nav>

  @if(session('ticket_ids'))
    <div class="alert alert-success">Vos billets ont été générés.</div>
  @endif

  @isset($no_pdf)
    <div class="alert alert-warning">Téléchargement PDF indisponible (package PDF non installé). Vous pouvez visualiser vos tickets en ligne.</div>
  @endisset

  <div class="row g-3">
    <div class="col-lg-8 vstack gap-3">
      <div class="panel-cream rounded-20">
        <div class="p-3">
          <h1 class="mb-1">Réservation confirmée</h1>
          <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $booking->event->location }} • <i class="bi bi-calendar3 mx-1"></i>{{ $booking->event->start_date }} → {{ $booking->event->end_date }}</div>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge text-bg-light border">Nom: {{ $booking->buyer_name }}</span>
            <span class="badge text-bg-light border">Email: {{ $booking->buyer_email }}</span>
          </div>

          <h5 class="mb-2">Billets</h5>
          @if($booking->tickets->count())
            <div class="vstack gap-2">
              @foreach($booking->tickets as $t)
                <div class="d-flex justify-content-between align-items-center border rounded p-2 bg-white">
                  <div>
                    <div class="fw-semibold">{{ $t->type->name }} — {{ number_format($t->type->price, 0, ',', ' ') }} CFA</div>
                    <div class="small text-muted">UID: {{ $t->uuid }}</div>
                  </div>
                  <div class="btn-group">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('tickets.show.uuid', $t->uuid) }}" target="_blank"><i class="bi bi-eye"></i> Voir</a>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('tickets.download.uuid', $t->uuid) }}"><i class="bi bi-download"></i> Télécharger</a>
                  </div>
                </div>
              @endforeach
            </div>
            @php($ticketsSubtotal = $booking->tickets->sum('type.price'))
            <div class="mt-3">Sous-total billets: <strong>{{ number_format($ticketsSubtotal, 0, ',', ' ') }} CFA</strong></div>
          @else
            <div class="text-muted">Aucun ticket.</div>
          @endif

          <div class="mt-1 fw-semibold">Total payé: {{ number_format($booking->total_amount, 0, ',', ' ') }} CFA</div>
        </div>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <button id="js-add-ics" class="btn btn-primary"><i class="bi bi-calendar-plus"></i> Ajouter au calendrier</button>
        <a href="{{ route('events.index') }}" class="btn btn-light">Explorer d'autres évènements</a>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary">Accueil</a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="panel-cream rounded-20 position-sticky" style="top:100px;">
        <div class="px-3 py-2 fw-semibold">Récapitulatif</div>
        <div class="p-3">
          @if(!empty($booking->event->image_path))
            <img src="{{ asset('storage/'.$booking->event->image_path) }}" alt="{{ $booking->event->name }}" class="w-100 rounded mb-2" style="max-height:150px;object-fit:cover;">
          @endif
          <div class="small text-muted mb-2">{{ $booking->event->location }} • {{ $booking->event->start_date }} → {{ $booking->event->end_date }}</div>
          <div class="small">Réservation au nom de <strong>{{ $booking->buyer_name }}</strong></div>
          <div class="small">Contact: <strong>{{ $booking->buyer_email }}</strong></div>
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
        // Accepts 'YYYY-MM-DD' or ISO-like; fallback to date-only
        const d = new Date(dstr);
        if (!isNaN(d.getTime())) {
          const y = d.getFullYear();
          const m = String(d.getMonth()+1).padStart(2,'0');
          const da = String(d.getDate()).padStart(2,'0');
          return `${y}${m}${da}`;
        }
      } catch(e){}
      // Fallback simple replace for 'YYYY-MM-DD'
      return String(dstr || '').replaceAll('-', '');
    }
    btn.addEventListener('click', function(){
      const title = @json($booking->event->name);
      const loc = @json($booking->event->location);
      const desc = `Réservation au nom de {{ addslashes($booking->buyer_name) }} — Contact: {{ addslashes($booking->buyer_email) }}`;
      const dtStart = toYYYYMMDD(@json($booking->event->start_date));
      const dtEnd = toYYYYMMDD(@json($booking->event->end_date));
      const now = new Date();
      const stamp = now.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
      const uid = `booking-{{ $booking->id }}@discoverbf`;
      const ics = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Discover BF//Event Booking//FR',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        `UID:${uid}`,
        `DTSTAMP:${stamp}`,
        `DTSTART;VALUE=DATE:${dtStart}`,
        `DTEND;VALUE=DATE:${dtEnd}`,
        `SUMMARY:${title.replace(/\n/g,' ')}`,
        `LOCATION:${(loc||'').replace(/\n/g,' ')}`,
        `DESCRIPTION:${desc.replace(/\n/g,' ')}`,
        'END:VEVENT',
        'END:VCALENDAR'
      ].join('\r\n');
      const blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `{{ Str::slug($booking->event->name) }}.ics`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      setTimeout(()=>URL.revokeObjectURL(url), 1000);
    });
  });
</script>
@endpush
