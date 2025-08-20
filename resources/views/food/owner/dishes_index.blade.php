@extends('layouts.tourist')

@section('title', 'Mes Plats')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><i class="bi bi-egg-fried me-2"></i>Gérer les plats</h2>
    <a href="{{ route('food.owner.restaurant.edit') }}" class="btn btn-outline-secondary"><i class="bi bi-shop"></i> Mon restaurant</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Ajouter un plat</h5>
          <form action="{{ route('food.owner.dishes.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label">Nom</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-5">
                <label class="form-label">Prix (CFA)</label>
                <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Catégorie</label>
                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}">
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Image (vignette)</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Galerie (plusieurs images)</label>
                <input type="file" name="gallery[]" class="form-control @error('gallery.*') is-invalid @enderror" accept="image/*" multiple>
                @error('gallery.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Vidéos (une URL par ligne)</label>
                <textarea name="video_urls" rows="2" class="form-control @error('video_urls') is-invalid @enderror">{{ old('video_urls') }}</textarea>
                @error('video_urls')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="mt-3">
              <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1"></i>Ajouter</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Mes plats</h5>
          @if($dishes->isEmpty())
            <div class="alert alert-info">Aucun plat pour le moment.</div>
          @else
            <div class="list-group">
              @foreach($dishes as $dish)
                <div class="list-group-item d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-3">
                    @php $thumb = $dish->image_path ?? ($dish->gallery[0] ?? null); @endphp
                    @if($thumb)
                      <img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/']) ? $thumb : asset('storage/'.$thumb) }}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                    @endif
                    <div>
                      <div class="fw-semibold">{{ $dish->name }}</div>
                      <div class="text-muted small">{{ number_format($dish->price, 0, ',', ' ') }} CFA • {{ $dish->category }}</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('food.owner.dishes.edit', $dish) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                    <form action="{{ route('food.owner.dishes.destroy', $dish) }}" method="post" onsubmit="return confirm('Supprimer ce plat ?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                    </form>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
