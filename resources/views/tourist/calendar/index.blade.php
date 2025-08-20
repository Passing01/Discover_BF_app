@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item active" aria-current="page">Calendrier</li>
    </ol>
  </nav>

  <div class="row g-3">
    <div class="col-lg-9">
      <div class="panel-cream rounded-20 p-3">
        <div id="calendar"></div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="panel-cream rounded-20 p-3">
        <div class="fw-semibold mb-2">Assistant IA</div>
        <div class="mb-2 small text-muted">Demandez des idées et ajoutez-les au calendrier.</div>
        <textarea id="ai-prompt" class="form-control mb-2" rows="5" placeholder="Ex: Planifie 3 activités culturelles à Ouagadougou le week-end prochain avec budget 50 000 CFA"></textarea>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="ai-auto-apply" checked>
          <label class="form-check-label" for="ai-auto-apply">Appliquer automatiquement au calendrier</label>
        </div>
        <div class="d-flex gap-2 mb-2">
          <button id="ai-start" class="btn btn-primary btn-sm"><i class="bi bi-stars me-1"></i>Lancer</button>
          <button id="ai-stop" class="btn btn-outline-secondary btn-sm" disabled>Arrêter</button>
        </div>
        <div id="ai-output" class="small bg-white rounded p-2" style="min-height:120px; max-height:240px; overflow:auto;"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'fr',
      height: 'auto',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      events: {
        url: '{{ route('tourist.calendar.feed') }}',
        failure: function() { console.error('Unable to load events'); }
      },
      eventClick: function(info){
        if (info.event.url) {
          info.jsEvent.preventDefault();
          window.location.href = info.event.url;
        }
      }
    });
    calendar.render();

    // IA streaming
    let es = null;
    const out = document.getElementById('ai-output');
    const promptEl = document.getElementById('ai-prompt');
    const btnStart = document.getElementById('ai-start');
    const btnStop = document.getElementById('ai-stop');
    const autoApply = document.getElementById('ai-auto-apply');
    let bufferText = '';

    function startStream(){
      const prompt = (promptEl.value || '').trim();
      if (!prompt) { promptEl.focus(); return; }
      out.textContent = '';
      bufferText = '';
      btnStart.setAttribute('disabled','disabled');
      btnStop.removeAttribute('disabled');
      const url = new URL('{{ route('assistant.ai.stream') }}', window.location.origin);
      url.searchParams.set('prompt', prompt);
      es = new EventSource(url.toString());
      es.onmessage = (e) => {
        try {
          if (e.data && e.data !== 'ok') {
            bufferText += e.data;
            out.textContent = bufferText;
            out.scrollTop = out.scrollHeight;
          }
        } catch(err){ console.error(err); }
      };
      es.addEventListener('done', async () => {
        stopStream();
        if (autoApply.checked) {
          // Simple parse: split by newline, keep first 6 non-empty lines
          const lines = bufferText.split(/\n|•|- /).map(l => l.trim()).filter(Boolean).slice(0, 6);
          for (const line of lines) {
            try {
              await fetch('{{ route('assistant.add') }}?'+new URLSearchParams({ label: line }), { method: 'GET', credentials: 'same-origin' });
            } catch(err) { console.warn('add fail', err); }
          }
          calendar.refetchEvents();
        }
      });
      es.onerror = () => { stopStream(); };
    }
    function stopStream(){
      if (es) { es.close(); es = null; }
      btnStop.setAttribute('disabled','disabled');
      btnStart.removeAttribute('disabled');
    }
    btnStart.addEventListener('click', startStream);
    btnStop.addEventListener('click', stopStream);
  });
</script>
@endpush
