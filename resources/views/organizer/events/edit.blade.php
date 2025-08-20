@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Modifier l’évènement</h1>

  <form method="POST" action="{{ route('organizer.events.update', $event) }}" enctype="multipart/form-data" class="panel-cream rounded-20 p-3 p-md-4">
    @csrf
    @method('PATCH')
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $event->name) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Lieu</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Date de début</label>
        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $event->start_date) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Date de fin</label>
        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $event->end_date) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Prix du billet (CFA)</label>
        <input type="number" step="0.01" name="ticket_price" class="form-control" value="{{ old('ticket_price', $event->ticket_price) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Catégorie</label>
        <input type="text" name="category" class="form-control" value="{{ old('category', $event->category) }}">
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="5" class="form-control">{{ old('description', $event->description) }}</textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Modèle de ticket (optionnel)</label>
        <select name="ticket_template_id" class="form-select">
          <option value="">— Aucun —</option>
          @isset($templates)
            @foreach($templates as $tpl)
              <option value="{{ $tpl->id }}" @selected(old('ticket_template_id', $event->ticket_template_id)===$tpl->id)>{{ $tpl->name }}</option>
            @endforeach
          @endisset
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Image de l’évènement (remplacer)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        @if($event->image_path)
          <div class="form-text mt-1">Image actuelle: <img src="{{ asset('storage/'.$event->image_path) }}" alt="image" style="height:50px"></div>
        @endif
      </div>

      <div class="col-12 border-top pt-3">
        <h5 class="mb-2">Prix du billet (plusieurs)</h5>
        <div id="ticket-types">
          @foreach($event->ticketTypes as $i => $tt)
            <div class="panel-cream rounded-20 p-3 mb-2">
              <div class="row g-2 align-items-end">
                <input type="hidden" name="ticket_types[{{ $i }}][id]" value="{{ $tt->id }}">
                <div class="col-md-3">
                  <label class="form-label">Nom</label>
                  <input type="text" name="ticket_types[{{ $i }}][name]" value="{{ old("ticket_types.$i.name", $tt->name) }}" class="form-control" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Prix (CFA)</label>
                  <input type="number" step="0.01" name="ticket_types[{{ $i }}][price]" value="{{ old("ticket_types.$i.price", $tt->price) }}" class="form-control">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Capacité</label>
                  <input type="number" name="ticket_types[{{ $i }}][capacity]" value="{{ old("ticket_types.$i.capacity", $tt->capacity) }}" class="form-control">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Vente du</label>
                  <input type="datetime-local" name="ticket_types[{{ $i }}][sales_start_at]" value="{{ old("ticket_types.$i.sales_start_at", optional($tt->sales_start_at)->format('Y-m-d\TH:i')) }}" class="form-control">
                </div>
                <div class="col-md-2">
                  <label class="form-label">au</label>
                  <input type="datetime-local" name="ticket_types[{{ $i }}][sales_end_at]" value="{{ old("ticket_types.$i.sales_end_at", optional($tt->sales_end_at)->format('Y-m-d\TH:i')) }}" class="form-control">
                </div>
                <div class="col-1 d-grid">
                  <button type="button" class="btn btn-outline-danger remove-ticket">&times;</button>
                </div>
                <div class="col-12">
                  <input type="text" name="ticket_types[{{ $i }}][description]" value="{{ old("ticket_types.$i.description", $tt->description) }}" class="form-control" placeholder="Description (optionnel)">
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <button type="button" id="add-ticket" class="btn btn-cream btn-sm mt-2">
          <i class="bi bi-plus-lg me-1"></i> Ajouter un prix
        </button>
      </div>
      <div class="col-12">
        <button class="btn btn-orange">Mettre à jour</button>
        <a href="{{ route('organizer.events.index') }}" class="btn btn-cream">Annuler</a>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  (function(){
    const container = document.getElementById('ticket-types');
    const addBtn = document.getElementById('add-ticket');
    function rowTemplate(index){
      return `
      <div class=\"panel-cream rounded-20 p-3 mb-2\">
        <div class=\"row g-2 align-items-end\">
          <div class=\"col-md-3\">
            <label class=\"form-label\">Nom</label>
            <input type=\"text\" name=\"ticket_types[${index}][name]\" class=\"form-control\" placeholder=\"Standard\" required>
          </div>
          <div class=\"col-md-2\">
            <label class=\"form-label\">Prix (CFA)</label>
            <input type=\"number\" step=\"0.01\" name=\"ticket_types[${index}][price]\" class=\"form-control\">
          </div>
          <div class=\"col-md-2\">
            <label class=\"form-label\">Capacité</label>
            <input type=\"number\" name=\"ticket_types[${index}][capacity]\" class=\"form-control\">
          </div>
          <div class=\"col-md-2\">
            <label class=\"form-label\">Vente du</label>
            <input type=\"datetime-local\" name=\"ticket_types[${index}][sales_start_at]\" class=\"form-control\">
          </div>
          <div class=\"col-md-2\">
            <label class=\"form-label\">au</label>
            <input type=\"datetime-local\" name=\"ticket_types[${index}][sales_end_at]\" class=\"form-control\">
          </div>
          <div class=\"col-1 d-grid\">
            <button type=\"button\" class=\"btn btn-outline-danger remove-ticket\">&times;</button>
          </div>
          <div class=\"col-12\">
            <input type=\"text\" name=\"ticket_types[${index}][description]\" class=\"form-control\" placeholder=\"Description (optionnel)\">
          </div>
        </div>
      </div>`;
    }
    let idx = {{ max(0, ($event->ticketTypes->count() ?? 0)) }};
    addBtn?.addEventListener('click', function(){
      container.insertAdjacentHTML('beforeend', rowTemplate(idx++));
    });
    container?.addEventListener('click', function(e){
      if(e.target.classList.contains('remove-ticket')){
        e.target.closest('.card').remove();
      }
    });
  })();
</script>
@endpush
