@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item active" aria-current="page">Sites touristiques</li>
    </ol>
  </nav>
  <h1 class="mb-1">Sites touristiques</h1>
  <p class="text-muted mb-3">Découvrez les sites à visiter au Burkina Faso.</p>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-3">
    @forelse($sites as $site)
      <div class="col-md-4">
        <div class="panel-cream rounded-20 h-100 d-flex flex-column">
          @if($site->photo_url)
            <img src="{{ $site->photo_url }}" alt="{{ $site->name }}" class="w-100 rounded-top" style="object-fit:cover; height:160px;">
          @endif
          <div class="p-3 d-flex flex-column gap-2 flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="mb-1">{{ $site->name }}</h5>
                <div class="text-muted small">{{ $site->city }} @if($site->category) · {{ $site->category }} @endif</div>
              </div>
            </div>
            @if($site->description)
              <p class="mb-2 small">{{ \Illuminate\Support\Str::limit($site->description, 120) }}</p>
            @endif
            <div class="mt-auto d-flex gap-2">
              <a href="{{ route('sites.show', $site->id) }}" class="btn btn-orange btn-sm flex-grow-1">Découvrir</a>
              <a href="{{ route('sites.show', $site->id) }}" class="btn btn-cream btn-sm">Détails</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">Aucun site disponible.</div>
      </div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center mt-3">{!! $sites->onEachSide(1)->links() !!}</div>
</div>
@endsection
