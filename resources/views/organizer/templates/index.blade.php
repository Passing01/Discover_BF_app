@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0 text-orange">Modèles de tickets</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('organizer.templates.create') }}" class="btn btn-orange"><i class="bi bi-plus-lg me-1"></i> Nouveau modèle</a>
      <a href="{{ route('organizer.templates.pdf.create') }}" class="btn btn-cream"><i class="bi bi-file-earmark-pdf me-1"></i> Importer un PDF</a>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if($templates->count())
    <div class="row g-3">
      @foreach($templates as $tpl)
        <div class="col-12 col-md-6 col-lg-4">
          <div class="panel-cream rounded-20 h-100 overflow-hidden">
            @if($tpl->bg_image_path)
              <div class="ratio ratio-16x9" style="background:#f2e9e1;">
                <div style="background-image:url('{{ asset('storage/'.$tpl->bg_image_path) }}');background-size:cover;background-position:center;border-bottom:1px solid rgba(0,0,0,.05);width:100%;height:100%;"></div>
              </div>
            @endif
            <div class="p-3 p-md-4">
              <h5 class="mb-1">{{ $tpl->name }}</h5>
              <div class="small text-muted d-flex flex-wrap gap-2">
                <span class="badge badge-soft">Prim: {{ $tpl->primary_color ?? '—' }}</span>
                <span class="badge badge-soft">Sec: {{ $tpl->secondary_color ?? '—' }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="d-flex gap-2">
                  <a class="btn btn-cream btn-sm" href="{{ route('organizer.templates.preview', $tpl) }}" target="_blank">Prévisualiser</a>
                  <a class="btn btn-cream btn-sm" href="{{ route('organizer.templates.download', $tpl) }}">Télécharger</a>
                </div>
                <a class="btn btn-orange btn-sm" href="{{ route('organizer.templates.edit', $tpl) }}">Éditer</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-3">{{ $templates->links() }}</div>
  @else
    <div class="alert alert-info rounded-20">Aucun modèle pour le moment.</div>
  @endif
</div>
@endsection
