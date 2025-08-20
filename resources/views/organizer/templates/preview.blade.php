@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  @isset($no_pdf)
    <div class="alert alert-warning">Téléchargement PDF indisponible (package PDF non installé). Vous pouvez imprimer la prévisualisation.</div>
  @endisset

  <h1 class="mb-3 text-orange">Prévisualisation du modèle: {{ $template->name }}</h1>

  <div class="panel-cream rounded-20">
    <div class="p-3 p-md-4">
      <div class="p-4 border position-relative" style="
        background-color: {{ $template->secondary_color ?? '#ffffff' }};
        color: {{ $template->primary_color ?? '#000000' }};
        font-family: {{ $template->font_family ?? 'inherit' }};
        min-height: 360px;
      ">
        @if($template->bg_image_path)
          <img src="{{ asset('storage/'.$template->bg_image_path) }}" alt="bg" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover; opacity: .15;">
        @endif
        <div class="position-relative">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-bold fs-4">Nom de l'évènement</div>
              <div class="text-muted">Lieu • Date</div>
            </div>
            <div class="text-end">
              <div class="fw-bold">{{ strtoupper($template->shape ?? 'rectangle') }}</div>
              <div class="small text-muted">{{ $template->logo_placement ?? 'logo: haut-droit' }}</div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-md-6">
              <div class="border rounded p-3 text-center">QR CODE</div>
              <div class="small text-muted mt-2">Position: {{ $template->qr_position ?? 'bas-gauche' }} • Taille: {{ $template->qr_size ?? 180 }}</div>
            </div>
            <div class="col-md-6">
              <ul class="list-unstyled mb-0">
                <li><strong>Ticket:</strong> VIP</li>
                <li><strong>Tarif:</strong> 20 000 CFA</li>
                <li><strong>UID:</strong> xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-3 d-flex gap-2">
    <a href="{{ route('organizer.templates.download', $template) }}" class="btn btn-orange">Télécharger PDF</a>
    <button class="btn btn-cream" onclick="window.print()">Imprimer</button>
    <a href="{{ route('organizer.templates.index') }}" class="btn btn-cream">Retour</a>
  </div>
</div>
@endsection
