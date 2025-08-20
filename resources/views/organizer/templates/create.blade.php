@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Nouveau modèle de ticket</h1>

  <form method="POST" action="{{ route('organizer.templates.store') }}" enctype="multipart/form-data" class="panel-cream rounded-20 p-3 p-md-4">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nom du modèle</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label class="form-label">Couleur primaire</label>
        <input list="color-names" type="text" name="primary_color" value="{{ old('primary_color') }}" class="form-control" placeholder="bleu | #0d6efd">
      </div>
      <div class="col-md-3">
        <label class="form-label">Couleur secondaire</label>
        <input list="color-names" type="text" name="secondary_color" value="{{ old('secondary_color') }}" class="form-control" placeholder="violet | #6610f2">
      </div>
      <div class="col-md-3">
        <label class="form-label">Couleur du texte</label>
        <input list="color-names" type="text" name="text_color" value="{{ old('text_color') }}" class="form-control" placeholder="noir | #000000">
      </div>
      <div class="col-md-6">
        <label class="form-label">Image de fond (optionnel)</label>
        <input type="file" name="bg_image" class="form-control" accept="image/*">
      </div>
      <div class="col-md-6">
        <label class="form-label">PDF du modèle (optionnel)</label>
        <input type="file" name="pdf_template" class="form-control" accept="application/pdf">
        <div class="form-text">Importez un PDF pour l'utiliser comme base. Vous pourrez placer les informations de l'acheteur ensuite.</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Couleur d'overlay</label>
        <input list="color-names" type="text" name="overlay_color" value="{{ old('overlay_color') }}" class="form-control" placeholder="noir | #000000">
      </div>
      <div class="col-md-3">
        <label class="form-label">Opacité overlay (0-1)</label>
        <input type="number" name="overlay_opacity" value="{{ old('overlay_opacity', 0.3) }}" class="form-control" min="0" max="1" step="0.05">
      </div>
      <div class="col-md-3">
        <label class="form-label">Forme</label>
        <select name="shape" class="form-select">
          <option value="">—</option>
          <option value="rect">Rectangulaire</option>
          <option value="rounded">Angles arrondis</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Police</label>
        <input type="text" name="font_family" value="{{ old('font_family') }}" class="form-control" placeholder="Inter, Arial...">
      </div>
      <div class="col-md-3">
        <label class="form-label">Rayon des coins (px)</label>
        <input type="number" name="corner_radius" value="{{ old('corner_radius', 16) }}" class="form-control" min="0" max="64">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="card_shadow_enabled" value="1" id="shadowCheck" {{ old('card_shadow_enabled', 1) ? 'checked' : '' }}>
          <label class="form-check-label" for="shadowCheck">Ombre de carte</label>
        </div>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="logo_enabled" value="1" id="logoEnabled" {{ old('logo_enabled') ? 'checked' : '' }}>
          <label class="form-check-label" for="logoEnabled">Afficher logo organisateur</label>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Position du logo</label>
        <select name="logo_position" class="form-select">
          <option value="top-left">Haut gauche</option>
          <option value="top-right" selected>Haut droite</option>
          <option value="bottom-left">Bas gauche</option>
          <option value="bottom-right">Bas droite</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Taille du logo (px)</label>
        <input type="number" name="logo_size" value="{{ old('logo_size', 56) }}" class="form-control" min="24" max="256">
      </div>
      <div class="col-md-4">
        <label class="form-label">Placement du logo</label>
        <select name="logo_placement" class="form-select">
          <option value="">—</option>
          <option value="top-left">Haut gauche</option>
          <option value="top-right">Haut droite</option>
          <option value="bottom-left">Bas gauche</option>
          <option value="bottom-right">Bas droite</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Placement de l'image</label>
        <select name="image_placement" class="form-select">
          <option value="">—</option>
          <option value="left">Gauche</option>
          <option value="right">Droite</option>
          <option value="full">Pleine largeur</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Position du QR</label>
        <select name="qr_position" class="form-select">
          <option value="">—</option>
          <option value="top-left">Haut gauche</option>
          <option value="top-right">Haut droite</option>
          <option value="bottom-left">Bas gauche</option>
          <option value="bottom-right">Bas droite</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Taille du QR (px)</label>
        <input type="number" name="qr_size" value="{{ old('qr_size', 128) }}" class="form-control" min="32" max="512">
      </div>
      <div class="col-12">
        <button class="btn btn-orange">Enregistrer</button>
        <a href="{{ route('organizer.templates.index') }}" class="btn btn-cream ms-2">Annuler</a>
      </div>
    </div>
  </form>
  <datalist id="color-names">
    <option value="noir"/>
    <option value="blanc"/>
    <option value="rouge"/>
    <option value="bleu"/>
    <option value="vert"/>
    <option value="jaune"/>
    <option value="orange"/>
    <option value="violet"/>
    <option value="gris"/>
    <option value="marron"/>
    <option value="rose"/>
  </datalist>
</div>
@endsection
