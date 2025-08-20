@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <h2 class="mb-3">Mes évènements</h2>
      <div class="card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr><th>Nom</th><th>Période</th><th>Lieu</th></tr>
            </thead>
            <tbody>
              @forelse($events as $ev)
                <tr>
                  <td>{{ $ev->name ?? '—' }}</td>
                  <td class="text-muted">{{ $ev->start_date ?? '—' }} → {{ $ev->end_date ?? '—' }}</td>
                  <td class="text-muted">{{ $ev->location ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted">Aucun évènement</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="p-3">{{ $events->links() }}</div>
      </div>

      <h3 class="mt-4 mb-3">Annonces pour vous</h3>
      @php($ads = $adsFeed)
      @include('partials.ads')
    </div>

    <div class="col-lg-4">
      @include('components.notifications-widget')

      <h5 class="mb-3">Promotions</h5>
      @php($ads = $adsSidebar)
      @include('partials.ads')
    </div>
  </div>
</div>
@endsection
