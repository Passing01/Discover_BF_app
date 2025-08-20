@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Créer un évènement</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold mb-1">Veuillez corriger les erreurs suivantes:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data" class="panel-cream rounded-20 p-3 p-md-4">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Lieu</label>
        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" required>
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Date de début</label>
        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Date de fin</label>
        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Prix du billet (CFA)</label>
        <input type="number" name="ticket_price" step="0.01" class="form-control @error('ticket_price') is-invalid @enderror" value="{{ old('ticket_price') }}">
        @error('ticket_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Catégorie</label>
        <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}">
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Modèle de ticket (optionnel)</label>
        <select name="ticket_template_id" class="form-select @error('ticket_template_id') is-invalid @enderror">
          <option value="">— Aucun —</option>
          @isset($templates)
            @foreach($templates as $tpl)
              <option value="{{ $tpl->id }}" @selected(old('ticket_template_id')===$tpl->id)>{{ $tpl->name }}</option>
            @endforeach
          @endisset
        </select>
        @error('ticket_template_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text"><a href="{{ route('organizer.templates.index') }}">Gérer les modèles</a></div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Image de l’évènement</label>
        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Obligatoire. JPG/PNG, 4 Mo max.</div>
      </div>

      <div class="col-12 border-top pt-3">
        <h5 class="mb-2">Prix du billet (plusieurs)</h5>
        <div id="ticket-types">
          <!-- Ticket type rows will be appended here -->
        </div>
        <button type="button" id="add-ticket" class="btn btn-cream btn-sm mt-2">
          <i class="bi bi-plus-lg me-1"></i> Ajouter un prix
        </button>
        <div class="form-text mt-1">Ex: Standard, VIP, Étudiant, Enfant, etc.</div>
      </div>
      <div class="col-12">
        <button class="btn btn-orange">Enregistrer</button>
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
      <div class="panel-cream rounded-20 p-3 mb-2">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Nom</label>
            <input type="text" name="ticket_types[${index}][name]" class="form-control" placeholder="Standard" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Prix (CFA)</label>
            <input type="number" step="0.01" name="ticket_types[${index}][price]" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">Capacité</label>
            <input type="number" name="ticket_types[${index}][capacity]" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">Vente du</label>
            <input type="datetime-local" name="ticket_types[${index}][sales_start_at]" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">au</label>
            <input type="datetime-local" name="ticket_types[${index}][sales_end_at]" class="form-control">
          </div>
          <div class="col-1 d-grid">
            <button type="button" class="btn btn-outline-danger remove-ticket">&times;</button>
          </div>
          <div class="col-12">
            <input type="text" name="ticket_types[${index}][description]" class="form-control" placeholder="Description (optionnel)">
          </div>
        </div>
      </div>`;
    }
    let idx = 0;
    // Add one default row on load for convenience
    container.insertAdjacentHTML('beforeend', rowTemplate(idx++));
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
