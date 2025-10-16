@extends('layouts.hotel')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="me-3 mb-2">
                    <h2 class="h3 mb-1">Calendrier des réservations</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-hotel me-1"></i> {{ $hotel->name }}
                    </p>
                </div>
                <div class="mb-2">
                    <a href="{{ route('hotel-manager.hotels.bookings.index', $hotel) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour aux réservations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

@push('styles')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    #calendar {
        min-height: 600px;
    }
    .fc-event {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: {!! $events !!},
            eventClick: function(info) {
                // Afficher les détails de la réservation
                const event = info.event;
                const roomNumber = event.extendedProps.room_number || 'Inconnu';
                const guestName = event.extendedProps.guest_name || 'Inconnu';
                const status = event.extendedProps.status || 'Inconnu';
                
                // Personnaliser le style du modal selon le statut
                let statusClass = 'primary';
                if (status === 'checked_in') statusClass = 'success';
                else if (status === 'cancelled') statusClass = 'danger';
                else if (status === 'pending') statusClass = 'warning';
                
                Swal.fire({
                    title: `Réservation #${event.id}`,
                    html: `
                        <div class="text-start">
                            <p><strong>Client:</strong> ${guestName}</p>
                            <p><strong>Chambre:</strong> ${roomNumber}</p>
                            <p><strong>Statut:</strong> <span class="badge bg-${statusClass}">${status}</span></p>
                            <p><strong>Du:</strong> ${event.start.toLocaleDateString()}</p>
                            <p><strong>Au:</strong> ${event.end ? event.end.toLocaleDateString() : ''}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Voir les détails',
                    showCancelButton: true,
                    cancelButtonText: 'Fermer',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const bookingId = event.id;
                        window.location.href = `/hotel-manager/hotels/{{ $hotel->id }}/bookings/${bookingId}`;
                    }
                });
            },
            eventDidMount: function(info) {
                // Personnaliser le style des événements selon leur statut
                const status = info.event.extendedProps.status;
                if (status === 'checked_in') {
                    info.el.style.backgroundColor = '#198754';
                    info.el.style.borderColor = '#198754';
                } else if (status === 'cancelled') {
                    info.el.style.backgroundColor = '#dc3545';
                    info.el.style.borderColor = '#dc3545';
                } else if (status === 'pending') {
                    info.el.style.backgroundColor = '#ffc107';
                    info.el.style.borderColor = '#ffc107';
                    info.el.style.color = '#000';
                }
            }
        });

        calendar.render();
    });
</script>
@endpush

@endsection
