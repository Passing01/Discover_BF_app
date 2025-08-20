@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Placer les informations sur le PDF</h1>

  @if(session('status'))
    <div class="alert alert-success rounded-20">{{ session('status') }}</div>
  @endif

  <div class="panel-cream rounded-20 p-3 p-md-4">
    <p class="small text-muted mb-4">
      Modèle: <strong>{{ $template->name }}</strong>
      @if($template->pdf_path)
        — PDF: <a href="{{ asset('storage/'.$template->pdf_path) }}" target="_blank" class="link-primary">ouvrir</a>
      @else
        — <span class="text-danger">Aucun PDF importé pour ce modèle.</span>
      @endif
    </p>

    <form action="{{ route('organizer.templates.overlay.save', $template) }}" method="post">
      @csrf
      <div class="mb-3">
        <label class="form-label">Page cible</label>
        <input type="number" name="page" min="1" value="{{ old('page', data_get($template->overlay_fields,'page', 1)) }}" class="form-control w-auto" style="max-width: 140px;" />
        @error('page')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <p class="small text-muted">Champs disponibles pour "value": buyer_name, buyer_email, event_name, ticket_uuid. Type: text ou qr.</p>
      </div>

      <div id="fields" class="d-flex flex-column gap-3">
        @php($fields = old('fields', data_get($template->overlay_fields,'fields', [])))
        @foreach(($fields ?: [ ['type'=>'text','value'=>'buyer_name','x'=>20,'y'=>40,'fontSize'=>12,'color'=>'#000000'] ]) as $idx => $f)
          <div class="border rounded-20 p-3">
            <div class="row g-2 align-items-end">
              <div class="col-6 col-md-2">
                <label class="form-label small">Type</label>
                <select name="fields[{{ $idx }}][type]" class="form-select">
                  <option value="text" @selected(($f['type'] ?? '')==='text')>text</option>
                  <option value="qr" @selected(($f['type'] ?? '')==='qr')>qr</option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label small">Value</label>
                <input name="fields[{{ $idx }}][value]" value="{{ $f['value'] ?? '' }}" class="form-control" placeholder="buyer_name" />
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label small">X (mm)</label>
                <input type="number" step="0.1" name="fields[{{ $idx }}][x]" value="{{ $f['x'] ?? 0 }}" class="form-control" />
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label small">Y (mm)</label>
                <input type="number" step="0.1" name="fields[{{ $idx }}][y]" value="{{ $f['y'] ?? 0 }}" class="form-control" />
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label small">Taille</label>
                <input type="number" name="fields[{{ $idx }}][fontSize]" value="{{ $f['fontSize'] ?? 12 }}" class="form-control" />
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label small">Couleur</label>
                <input type="text" name="fields[{{ $idx }}][color]" value="{{ $f['color'] ?? '#000000' }}" class="form-control" />
              </div>
            </div>
            <div class="mt-2">
              <label class="form-label small">Texte fixe (si type=text et value vide)</label>
              <input name="fields[{{ $idx }}][text]" value="{{ $f['text'] ?? '' }}" class="form-control" />
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-orange">Enregistrer</button>
        <a href="{{ route('organizer.templates.index') }}" class="btn btn-cream">Retour</a>
      </div>
    </form>

    <div class="mt-4 small text-muted">
      Astuce: Les coordonnées X/Y sont en millimètres à partir du coin supérieur gauche de la page du PDF.
    </div>
  </div>
</div>
@endsection
