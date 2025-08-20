@if(isset($ads) && count($ads))
  <div class="d-grid gap-3">
    @foreach($ads as $ad)
      <div class="card border-0 shadow-sm">
        @if($ad->image_path)
          <img src="{{ asset(ltrim($ad->image_path,'/')) }}" class="card-img-top" alt="{{ $ad->title }}">
        @endif
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">{{ $ad->title }}</h6>
            @if($ad->target_url)
              <a href="{{ $ad->target_url }}" target="_blank" class="btn btn-sm btn-primary">{{ $ad->cta_text ?? 'Voir' }}</a>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
