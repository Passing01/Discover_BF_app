@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <h2 class="mb-3">Mes hôtels</h2>
      <div class="card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr><th>Nom</th><th>Ville</th><th>Chambres</th></tr>
            </thead>
            <tbody>
              @forelse($hotels as $h)
                <tr>
                  <td>{{ $h->name }}</td>
                  <td class="text-muted">{{ $h->city ?? '—' }}</td>
                  <td class="text-muted">{{ $h->rooms_count }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted">Aucun hôtel</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="p-3">{{ $hotels->links() }}</div>
      </div>

      <h3 class="mt-4 mb-3">Annonces pour vous</h3>
      @php($ads = $adsFeed)
      @include('partials.ads')
    </div>

    <div class="col-lg-4">
      @include('components.notifications-widget')

      <x-ad-banner placement="dashboard_sidebar" />
      <h5 class="mb-3">Promotions</h5>
      @php($ads = $adsSidebar)
      @include('partials.ads')
    </div>
  </div>
</div>
@endsection
