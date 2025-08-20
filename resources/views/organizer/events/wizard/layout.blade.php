@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <h1 class="h4 mb-0 text-orange">Créer un évènement</h1>
    <span class="ms-3 text-muted">Étape {{ $currentStep }} / 5</span>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="panel-cream rounded-20 p-3 p-md-4 h-100">
        @yield('wizard-form')
      </div>
    </div>
    <div class="col-lg-5">
      @include('organizer.events.wizard.partials.preview', ['draft' => $draft ?? []])
    </div>
  </div>

  <div class="mt-3 panel-cream rounded-20 p-2">
    <div class="progress" role="progressbar" aria-label="Progression">
      @php($pct = min(100, max(0, ($currentStep/5)*100)))
      <div class="progress-bar" style="width: {{ $pct }}%">{{ (int)$pct }}%</div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const flag = @json(session('open_templates_modal', false));
    if (flag) {
      const modalEl = document.getElementById('templatesModal');
      if (modalEl) {
        const m = new bootstrap.Modal(modalEl);
        m.show();
      }
    }
  });
</script>
@endpush

{{-- Modal suggestions disponible sur chaque étape --}}
@include('organizer.events.wizard.partials.templates-modal')
