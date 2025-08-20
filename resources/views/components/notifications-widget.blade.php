@php
  $user = auth()->user();
  $items = $user
    ? \App\Models\Notification::where('user_id', $user->id)
        ->orderByDesc('sent_at')
        ->orderByDesc('created_at')
        ->limit(5)->get()
    : collect();
@endphp
<div class="card dash-card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="dash-section-title"><i class="bi bi-bell me-1"></i> Notifications</div>
      <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-light">Voir tout</a>
    </div>
    <div class="vstack gap-2">
      @forelse($items as $it)
        <div class="border rounded p-2 d-flex justify-content-between align-items-start gap-2 @if(!$it->read) bg-warning bg-opacity-10 @endif">
          <div>
            <div class="fw-semibold">
              @if($it->type === 'festival_alert')
                <span class="badge text-bg-danger me-1">Festival</span>
              @elseif($it->type)
                <span class="badge text-bg-secondary me-1">{{ $it->type }}</span>
              @endif
              {{ $it->title }}
            </div>
            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($it->message, 140) }}</div>
            <div class="small text-muted">{{ optional($it->sent_at)->diffForHumans() ?? $it->created_at->diffForHumans() }}</div>
          </div>
          @if(!$it->read)
            <form method="POST" action="{{ route('user.notifications.read', $it) }}" class="ms-auto">
              @csrf
              <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check2"></i></button>
            </form>
          @endif
        </div>
      @empty
        <div class="text-muted small">Aucune notification récente.</div>
      @endforelse
    </div>
  </div>
</div>
