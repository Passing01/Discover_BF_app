@extends('layouts.tourist')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.events.index') }}">Événements</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $event->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                @if($event->hasMedia('featured'))
                    <img src="{{ $event->getFirstMediaUrl('featured', 'large') }}" class="card-img-top" alt="{{ $event->title }}">
                @endif
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h1 class="h3 mb-0">{{ $event->title }}</h1>
                        <span class="badge bg-{{ $event->status_color }}">{{ $event->status_label }}</span>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if($event->category)
                            <span class="badge bg-primary">
                                <i class="{{ $event->category->icon }} me-1"></i>
                                {{ $event->category->name }}
                            </span>
                        @endif
                        
                        @if($event->is_featured)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star me-1"></i> À la une
                            </span>
                        @endif
                        
                        @if($event->is_free)
                            <span class="badge bg-success">
                                <i class="fas fa-tag me-1"></i> Gratuit
                            </span>
                        @endif
                    </div>
                    
                    <div class="event-meta mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="far fa-calendar-alt me-2 text-primary"></i>
                            <div>
                                <div class="fw-bold">Date et heure</div>
                                <div>{{ $event->formatted_date_range }}</div>
                                @if($event->timezone)
                                    <small class="text-muted">Fuseau horaire: {{ $event->timezone }}</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-{{ $event->location_type === 'online' ? 'video' : 'map-marker-alt' }} me-2 mt-1 text-primary"></i>
                            <div>
                                <div class="fw-bold">
                                    {{ $event->location_type === 'online' ? 'Événement en ligne' : 'Lieu' }}
                                </div>
                                @if($event->location_type === 'online')
                                    <a href="{{ $event->meeting_url }}" target="_blank" class="text-decoration-none">
                                        {{ $event->meeting_url }}
                                    </a>
                                    @if($event->meeting_instructions)
                                        <div class="mt-2">
                                            <div class="fw-bold">Instructions de connexion :</div>
                                            <div class="bg-light p-2 rounded">
                                                {!! nl2br(e($event->meeting_instructions)) !!}
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div>{{ $event->location_name }}</div>
                                    <div>{{ $event->address }}</div>
                                    <div>
                                        @if($event->postal_code){{ $event->postal_code }} @endif
                                        {{ $event->city }}
                                        @if($event->region), {{ $event->region }} @endif
                                    </div>
                                    <div>{{ $event->country_name }}</div>
                                    
                                    @if($event->latitude && $event->longitude)
                                        <div class="mt-2">
                                            <div id="map" style="height: 200px; width: 100%;"></div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        @if($event->requires_registration)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-users me-2 text-primary"></i>
                                <div>
                                    <div class="fw-bold">Inscriptions</div>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                            <div class="progress-bar bg-{{ $event->registration_progress >= 100 ? 'danger' : 'success' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ min($event->registration_progress, 100) }}%" 
                                                 aria-valuenow="{{ $event->registration_progress }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="small">
                                            {{ $event->registrations_count }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @else
                                                inscrits
                                            @endif
                                        </span>
                                    </div>
                                    @if($event->registration_deadline)
                                        <div class="small mt-1">
                                            Date limite d'inscription : 
                                            {{ $event->registration_deadline->format('d/m/Y à H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($event->tags->count() > 0)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-tags me-2 text-primary"></i>
                                <div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($event->tags as $tag)
                                            <span class="badge bg-secondary">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="event-description mb-4">
                        <h5 class="mb-3">Description</h5>
                        {!! $event->formatted_description !!}
                    </div>
                    
                    @if($event->gallery->count() > 0)
                        <div class="event-gallery mb-4">
                            <h5 class="mb-3">Galerie photos</h5>
                            <div class="row g-2">
                                @foreach($event->gallery as $media)
                                    <div class="col-4 col-md-3">
                                        <a href="{{ $media->getUrl() }}" data-fancybox="gallery">
                                            <img src="{{ $media->getUrl('thumb') }}" alt="" class="img-fluid rounded">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="far fa-calendar-plus me-1"></i>
                            Créé le {{ $event->created_at->format('d/m/Y') }}
                            @if($event->created_at != $event->updated_at)
                                <span class="mx-2">•</span>
                                <i class="far fa-edit me-1"></i>
                                Modifié le {{ $event->updated_at->format('d/m/Y') }}
                            @endif
                        </div>
                        
                        <div class="btn-group">
                            <a href="{{ route('site.events.edit', $event) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Actions supplémentaires</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if($event->status === 'draft')
                                    <li>
                                        <form action="{{ route('site.events.update-status', $event) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="published">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-check-circle text-success me-2"></i> Publier
                                            </button>
                                        </form>
                                    </li>
                                @elseif($event->status === 'published')
                                    <li>
                                        <form action="{{ route('site.events.update-status', $event) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="dropdown-item" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cet événement ?')">
                                                <i class="fas fa-times-circle text-danger me-2"></i> Annuler l'événement
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                
                                @if($event->requires_registration)
                                    <li>
                                        <a href="{{ route('site.events.registrations', $event) }}" class="dropdown-item">
                                            <i class="fas fa-users me-2"></i> Voir les inscriptions
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('site.events.export-registrations', $event) }}" class="dropdown-item">
                                            <i class="fas fa-file-export me-2"></i> Exporter les inscriptions
                                        </a>
                                    </li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li>
                                    <form action="{{ route('site.events.destroy', $event) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')">
                                            <i class="far fa-trash-alt me-2"></i> Supprimer
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($event->status === 'published' && $event->requires_registration && $event->can_register)
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">S'inscrire à l'événement</h5>
                        
                        <form action="{{ route('site.events.register', $event) }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            @if($event->collect_phone)
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            
                            @if($event->collect_company)
                                <div class="mb-3">
                                    <label for="company" class="form-label">Entreprise/Organisation</label>
                                    <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                           id="company" name="company" value="{{ old('company') }}">
                                    @error('company')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            
                            @if($event->collect_position)
                                <div class="mb-3">
                                    <label for="position" class="form-label">Poste occupé</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                           id="position" name="position" value="{{ old('position') }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            
                            @if(!empty($event->custom_questions))
                                <div class="mb-4">
                                    <h6 class="mb-3">Questions supplémentaires</h6>
                                    
                                    @foreach($event->custom_questions as $index => $question)
                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{ $question['question'] }}
                                                @if(!empty($question['required']))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            
                                            @if(in_array($question['type'], ['select', 'radio']))
                                                @foreach($question['options'] as $option)
                                                    <div class="form-check {{ $question['type'] === 'radio' ? 'form-check' : 'form-check' }}">
                                                        <input class="form-check-input" 
                                                               type="{{ $question['type'] === 'radio' ? 'radio' : 'checkbox' }}" 
                                                               name="custom_answers[{{ $index }}]" 
                                                               id="custom_{{ $index }}_{{ $loop->index }}" 
                                                               value="{{ $option }}"
                                                               @if(!empty($question['required'])) required @endif>
                                                        <label class="form-check-label" for="custom_{{ $index }}_{{ $loop->index }}">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @elseif($question['type'] === 'checkbox')
                                                @foreach($question['options'] as $optionIndex => $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="custom_answers[{{ $index }}][]" 
                                                               id="custom_{{ $index }}_{{ $optionIndex }}" 
                                                               value="{{ $option }}">
                                                        <label class="form-check-label" for="custom_{{ $index }}_{{ $optionIndex }}">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <input type="{{ $question['type'] === 'textarea' ? 'textarea' : 'text' }}" 
                                                       class="form-control @error('custom_answers.' . $index) is-invalid @enderror" 
                                                       name="custom_answers[{{ $index }}]"
                                                       @if($question['type'] === 'textarea') rows="3" @endif
                                                       @if(!empty($question['required'])) required @endif>
                                                @error('custom_answers.' . $index)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Confirmer mon inscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            @if($event->hasMedia('gallery'))
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Galerie photos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            @foreach($event->getMedia('gallery')->take(4) as $index => $media)
                                <div class="col-6">
                                    <a href="{{ $media->getUrl() }}" data-fancybox="gallery-sidebar" 
                                       class="d-block position-relative" style="padding-bottom: 100%;">
                                        <img src="{{ $media->getUrl('thumb') }}" 
                                             alt="" 
                                             class="position-absolute w-100 h-100 object-fit-cover">
                                        @if($index === 3 && $event->getMedia('gallery')->count() > 4)
                                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                                <span class="text-white fw-bold">
                                                    +{{ $event->getMedia('gallery')->count() - 4 }} photos
                                                </span>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            @if($event->hasRelatedEvents())
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Autres événements similaires</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($event->relatedEvents(3) as $relatedEvent)
                            <a href="{{ route('site.events.show', $relatedEvent) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    @if($relatedEvent->hasMedia('featured'))
                                        <div class="flex-shrink-0 me-3" style="width: 60px; height: 40px; overflow: hidden;">
                                            <img src="{{ $relatedEvent->getFirstMediaUrl('featured', 'thumb') }}" 
                                                 alt="" 
                                                 class="img-fluid"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $relatedEvent->title }}</h6>
                                        <small class="text-muted">
                                            {{ $relatedEvent->start_date->format('d/m/Y') }}
                                            @if($relatedEvent->end_date && !$relatedEvent->start_date->isSameDay($relatedEvent->end_date))
                                                - {{ $relatedEvent->end_date->format('d/m/Y') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Partager cet événement</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('site.events.show', $event)) }}" 
                           target="_blank" 
                           class="btn btn-outline-primary btn-sm rounded-circle"
                           title="Partager sur Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('site.events.show', $event)) }}&text={{ urlencode($event->title) }}" 
                           target="_blank" 
                           class="btn btn-outline-info btn-sm rounded-circle"
                           title="Partager sur Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="whatsapp://send?text={{ urlencode($event->title . ' - ' . route('site.events.show', $event)) }}" 
                           target="_blank" 
                           class="btn btn-outline-success btn-sm rounded-circle"
                           title="Partager sur WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="mailto:?subject={{ rawurlencode($event->title) }}&body={{ rawurlencode('Je vous invite à cet événement : ' . route('site.events.show', $event)) }}" 
                           class="btn btn-outline-secondary btn-sm rounded-circle"
                           title="Partager par email">
                            <i class="far fa-envelope"></i>
                        </a>
                        <button type="button" 
                                class="btn btn-outline-dark btn-sm rounded-circle copy-link" 
                                data-url="{{ route('site.events.show', $event) }}"
                                title="Copier le lien">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($event->hasMap())
    @push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
        <script>
            function initMap() {
                const location = { lat: {{ $event->latitude }}, lng: {{ $event->longitude }} };
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 15,
                    center: location,
                });
                
                new google.maps.Marker({
                    position: location,
                    map: map,
                    title: "{{ addslashes($event->location_name) }}"
                });
            }
            
            // Initialiser la carte lorsque le document est prêt
            document.addEventListener('DOMContentLoaded', function() {
                initMap();
            });
        </script>
    @endpush
@endif

@push('scripts')
    <script>
        // Copier le lien dans le presse-papier
        document.querySelectorAll('.copy-link').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    const originalTitle = this.getAttribute('title');
                    this.setAttribute('title', 'Lien copié !');
                    const tooltip = new bootstrap.Tooltip(this);
                    tooltip.show();
                    
                    setTimeout(() => {
                        tooltip.hide();
                        this.setAttribute('title', originalTitle);
                    }, 2000);
                });
            });
        });
        
        // Initialiser les tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .event-meta i {
            width: 24px;
            text-align: center;
        }
        .object-fit-cover {
            object-fit: cover;
        }
    </style>
@endpush
@endsection
