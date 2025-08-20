@props(['placement'])
@php
  $ads = \App\Models\Ad::activeFor($placement)->orderByDesc('weight')->limit(5)->get();
  $ad = $ads->isNotEmpty() ? $ads->random() : null;
@endphp
@if($ad)
  <div class="my-3">
    <a href="{{ $ad->target_url ?? '#' }}" class="text-decoration-none d-block">
      @if($ad->image_path)
        <img src="{{ \Illuminate\Support\Str::startsWith($ad->image_path, ['http://','https://','/']) ? $ad->image_path : asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title ?? 'Annonce' }}" class="img-fluid rounded-3 shadow-sm w-100" />
      @else
        <div class="border rounded-3 p-3 text-center bg-light">
          <div class="fw-semibold">{{ $ad->title ?? 'Annonce' }}</div>
          @if($ad->cta_text)
            <div class="text-muted small">{{ $ad->cta_text }}</div>
          @endif
        </div>
      @endif
    </a>
  </div>
@endif
