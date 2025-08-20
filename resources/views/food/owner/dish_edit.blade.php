@extends('layouts.tourist')

@section('title', 'Modifier un plat')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Modifier le plat</h2>
    <a href="{{ route('food.owner.dishes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="{{ route('food.owner.dishes.update', $dish) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="row g-3">
          <div class="col-md-7">
            <label class="form-label">Nom</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $dish->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-5">
            <label class="form-label">Prix (CFA)</label>
            <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $dish->price) }}" required>
            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Catégorie</label>
            <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $dish->category) }}">
            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" value="1" id="is_available" name="is_available" {{ old('is_available', $dish->is_available) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_available">Disponible</label>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $dish->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Image (vignette)</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @php $thumb = $dish->image_path ?? ($dish->gallery[0] ?? null); @endphp
            @if($thumb)
              <div class="mt-2"><img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/']) ? $thumb : asset('storage/'.$thumb) }}" style="height:90px;object-fit:cover;border-radius:8px;"></div>
            @endif
          </div>
          <div class="col-md-6">
            <label class="form-label">Galerie (ajouter des images)</label>
            <input type="file" name="gallery[]" class="form-control @error('gallery.*') is-invalid @enderror" accept="image/*" multiple>
            @error('gallery.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if(!empty($dish->gallery))
              <div class="d-flex gap-2 flex-wrap mt-2">
                @foreach($dish->gallery as $g)
                  <img src="{{ \Illuminate\Support\Str::startsWith($g, ['http://','https://','/']) ? $g : asset('storage/'.$g) }}" style="width:70px;height:70px;object-fit:cover;border-radius:6px;">
                @endforeach
              </div>
            @endif
          </div>

          <div class="col-12">
            <label class="form-label">Vidéos (une URL par ligne)</label>
            <textarea name="video_urls" rows="2" class="form-control @error('video_urls') is-invalid @enderror">{{ old('video_urls', isset($dish->video_urls) ? implode("\n", $dish->video_urls) : '') }}</textarea>
            @error('video_urls')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Enregistrer</button>
          <a href="{{ route('food.owner.dishes.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
