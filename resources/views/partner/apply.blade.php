@extends('layouts.site')

@section('content')
<div>
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Devenir partenaire') }}</li>
            </ol>
        </nav>
        <h1 class="mb-1">{{ __('Devenir partenaire') }}</h1>
        <p class="text-muted mb-3">{{ __('Rejoignez notre écosystème: guides, organisateurs d’évènements, chauffeurs, hôteliers.') }}</p>
        <div class="row justify-content-center">
            <div class="col-lg-8">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">{{ __('Soumettre une candidature partenaire') }}</div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        {{ __('Soumettez votre candidature pour devenir guide, organisateur d’évènements, chauffeur ou hôtelier. Un administrateur examinera votre demande.') }}
                    </p>

                    <form method="POST" action="{{ route('partner.apply.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="requested_role" :value="__('Rôle souhaité')" />
                            <select id="requested_role" name="requested_role" class="form-select mt-1" required>
                                <option value="">{{ __('Choisir un rôle…') }}</option>
                                <option value="guide">{{ __('Guide') }}</option>
                                <option value="event_organizer">{{ __('Organisateur d’évènements') }}</option>
                                <option value="driver">{{ __('Chauffeur') }}</option>
                                <option value="hotel_manager">{{ __('Hôtelier / Gestionnaire d’hôtel') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('requested_role')" class="mt-2" />
                            <div class="form-text">{{ __('Sélectionnez le rôle qui correspond le mieux à votre activité.') }}</div>
                        </div>

                        <div>
                            <x-input-label for="motivation" :value="__('Votre motivation')" />
                            <textarea id="motivation" name="motivation" rows="5" class="form-control mt-1" placeholder="{{ __('Présentez brièvement votre expérience et vos atouts…') }}" required>{{ old('motivation') }}</textarea>
                            <x-input-error :messages="$errors->get('motivation')" class="mt-2" />
                            <div class="form-text">{{ __('Quelques lignes suffisent (expérience, langues, zone, disponibilité).') }}</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <x-input-label for="experience_years" :value="__('Années d’expérience (optionnel)')" />
                                <x-text-input id="experience_years" type="number" name="experience_years" min="0" max="60" :value="old('experience_years')" class="mt-1 w-100" />
                                <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                            </div>
                            <div class="col-sm-6">
                                <x-input-label for="languages" :value="__('Langues parlées (optionnel)')" />
                                <x-text-input id="languages" type="text" name="languages" :value="old('languages')" class="mt-1 w-100" placeholder="Français, Mooré, Dioula, …" />
                                <x-input-error :messages="$errors->get('languages')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="locations" :value="__('Zones d’intervention (optionnel)')" />
                            <x-text-input id="locations" type="text" name="locations" :value="old('locations')" class="mt-1 w-100" placeholder="Ouagadougou, Bobo-Dioulasso, Pays Lobi, …" />
                            <x-input-error :messages="$errors->get('locations')" class="mt-2" />
                        </div>

                        <div class="pt-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="bi bi-shield-lock me-1"></i>{{ __('Vos informations sont confidentielles et uniquement utilisées pour l’évaluation.') }}</span>
                            <x-primary-button>
                                <i class="bi bi-send me-1"></i> {{ __('Envoyer ma candidature') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
