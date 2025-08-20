@extends('layouts.tourist')

@section('content')
<div class="container mx-auto max-w-2xl py-6">
  <h1 class="text-2xl font-semibold mb-4">Logo de l'organisateur</h1>

  @if(session('status'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
  @endif

  <div class="bg-white shadow rounded p-4">
    <form action="{{ route('organizer.profile.logo.update') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Importer un logo</label>
        <input type="file" name="logo" accept="image/*" class="border rounded w-full p-2" required />
        @error('logo')
          <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Aperçu actuel</label>
        @php($path = $profile->logo_path)
        @if($path)
          <img src="{{ asset('storage/'.$path) }}" alt="Logo actuel" class="h-20 object-contain border rounded p-1 bg-white" />
        @else
          <div class="text-gray-500 text-sm">Aucun logo enregistré.</div>
        @endif
      </div>
      <div class="flex gap-2">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">Enregistrer</button>
        <a href="{{ route('organizer.events.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-4 py-2 rounded">Annuler</a>
      </div>
    </form>
  </div>
</div>
@endsection
