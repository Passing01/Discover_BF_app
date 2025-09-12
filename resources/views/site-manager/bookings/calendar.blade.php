@extends('layouts.site-manager')

@push('styles')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    #calendar {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .fc-toolbar {
        padding: 1rem;
        margin-bottom: 0 !important;
        background-color: #f8f9fa;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    
    .fc-toolbar-title {
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    .fc-button {
        background-color: #fff;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    
    .fc-button-primary:not(:disabled).fc-button-active, 
    .fc-button-primary:not(:disabled):active {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .fc-button-primary:not(:disabled).fc-button-active:focus, 
    .fc-button-primary:not(:disabled):active:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .fc-button-primary:disabled {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #6c757d;
    }
    
    .fc-daygrid-day-number {
        color: #212529;
        font-weight: 500;
    }
    
    .fc-col-header-cell-cushion {
        color: #495057;
        font-weight: 600;
        text-decoration: none;
    }
    
    .fc-day-today {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .fc-event {
        border-radius: 0.25rem;
        font-size: 0.875rem;
        padding: 0.15rem 0.5rem;
        margin-bottom: 0.25rem;
        border: none;
        cursor: pointer;
    }
    
    .fc-event-pending {
        background-color: #ffc107;
        color: #000;
    }
    
    .fc-event-confirmed {
        background-color: #198754;
        color: #fff;
    }
    
    .fc-event-completed {
        background-color: #6c757d;
        color: #fff;
    }
    
    .fc-event-cancelled {
        background-color: #dc3545;
        color: #fff;
        text-decoration: line-through;
    }
    
    .fc-daygrid-event-dot {
        display: none;
    }
    
    .fc-daygrid-event-harness {
        margin-bottom: 0.25rem;
    }
    
    .fc-daygrid-day-events {
        min-height: 0;
    }
    
    .fc-daygrid-day-bottom {
        margin-top: 0.25rem;
    }
    
    .fc-daygrid-dot-event .fc-event-title {
        font-weight: 500;
    }
    
    .fc-daygrid-more-link {
        font-size: 0.75rem;
        color: #6c757d;
        text-decoration: none;
    }
    
    .fc-daygrid-more-link:hover {
        color: #0d6efd;
    }
    
    .fc-daygrid-day.fc-day-today {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .fc-daygrid-day-number {
        padding: 0.5rem;
    }
    
    .fc-toolbar-chunk {
        display: flex;
        align-items: center;
    }
    
    .fc-toolbar-chunk:not(:last-child) {
        margin-right: 1rem;
    }
    
    .fc-today-button {
        margin-left: 0.5rem;
    }
    
    .fc-prev-button, .fc-next-button {
        padding: 0.25rem 0.5rem;
    }
    
    .fc-prev-button {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .fc-next-button {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        margin-left: -1px;
    }
    
    .fc-daygrid-day-frame {
        min-height: 100px;
    }
    
    .fc-day-today .fc-daygrid-day-number {
        color: #0d6efd;
        font-weight: 700;
    }
    
    .fc-day-other .fc-daygrid-day-number {
        color: #adb5bd;
    }
    
    .fc-daygrid-day.fc-day-other {
        background-color: #f8f9fa;
    }
    
    .fc-daygrid-day-events {
        min-height: 1.5em;
    }
    
    .fc-daygrid-event-harness {
        margin-bottom: 0.25rem;
    }
    
    .fc-event-time {
        font-weight: 500;
    }
    
    .fc-event-title {
        margin-left: 0.25rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .fc-toolbar-chunk {
            margin-bottom: 0.5rem;
        }
        
        .fc-toolbar-title {
            font-size: 1.1rem;
        }
        
        .fc-button {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }
    
    /* Custom scrollbar for event list */
    .event-list-container {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .event-list-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .event-list-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .event-list-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .event-list-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Event list item */
    .event-item {
        border-left: 4px solid #0d6efd;
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        transition: all 0.2s;
    }
    
    .event-item:hover {
        background-color: #e9ecef;
        transform: translateX(2px);
    }
    
    .event-item.pending {
        border-left-color: #ffc107;
    }
    
    .event-item.confirmed {
        border-left-color: #198754;
    }
    
    .event-item.completed {
        border-left-color: #6c757d;
    }
    
    .event-item.cancelled {
        border-left-color: #dc3545;
        opacity: 0.7;
    }
    
    .event-time {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .event-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .event-site {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .event-visitors {
        font-size: 0.75rem;
        color: #495057;
        margin-top: 0.25rem;
    }
    
    /* Legend */
    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 3px;
        margin-right: 0.5rem;
    }
    
    .legend-pending { background-color: #ffc107; }
    .legend-confirmed { background-color: #198754; }
    .legend-completed { background-color: #6c757d; }
    .legend-cancelled { background-color: #dc3545; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Calendrier des réservations</h1>
        <p class="mb-0">Visualisez et gérez les réservations de vos sites touristiques</p>
    </div>
    <div>
        <a href="{{ route('site-manager.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Vue liste
        </a>
    </div>
</div>

<div class="calendar-legend mb-4">
    <div class="legend-item">
        <div class="legend-color legend-pending"></div>
        <span>En attente</span>
    </div>
    <div class="legend-item">
        <div class="legend-color legend-confirmed"></div>
        <span>Confirmée</span>
    </div>
    <div class="legend-item">
        <div class="legend-color legend-completed"></div>
        <span>Terminée</span>
    </div>
    <div class="legend-item">
        <div class="legend-color legend-cancelled"></div>
        <span>Annulée</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Réservations du <span id="selected-date">{{ now()->format('d/m/Y') }}</span></h6>
            </div>
            <div class="card-body p-0">
                <div class="event-list-container p-3">
                    <div id="event-list">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-calendar3" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Sélectionnez une date pour voir les réservations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Détails de la réservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" class="btn btn-primary" id="viewBookingBtn">
                    <i class="bi bi-eye me-1"></i> Voir les détails
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var selectedDate = new Date();
    
    // Initialize the calendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        firstDay: 1, // Start week on Monday
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        events: {
            url: '{{ route("site-manager.calendar.events") }}',
            method: 'GET',
            failure: function() {
                alert('Erreur lors du chargement des réservations');
            }
        },
        eventClick: function(info) {
            var event = info.event;
            var bookingId = event.id;
            
            // Update modal title
            document.getElementById('eventModalLabel').textContent = 'Détails de la réservation #' + bookingId;
            
            // Set the view button href
            document.getElementById('viewBookingBtn').href = '/site-manager/bookings/' + bookingId;
            
            // Show loading spinner
            document.getElementById('eventModalBody').innerHTML = `
                <div class="text-center my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            `;
            
            // Load booking details via AJAX
            fetch(`/site-manager/bookings/${bookingId}/details`)
                .then(response => response.json())
                .then(data => {
                    var statusClass = '';
                    var statusText = '';
                    
                    switch(data.status) {
                        case 'pending':
                            statusClass = 'text-warning';
                            statusText = 'En attente';
                            break;
                        case 'confirmed':
                            statusClass = 'text-success';
                            statusText = 'Confirmée';
                            break;
                        case 'completed':
                            statusClass = 'text-secondary';
                            statusText = 'Terminée';
                            break;
                        case 'cancelled':
                            statusClass = 'text-danger';
                            statusText = 'Annulée';
                            break;
                    }
                    
                    var html = `
                        <div class="mb-3">
                            <h6 class="mb-1">${data.site_name}</h6>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-geo-alt"></i> ${data.site_city}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge ${statusClass} bg-opacity-10 p-2">
                                    <i class="bi ${data.status === 'confirmed' ? 'bi-check-circle' : (data.status === 'cancelled' ? 'bi-x-circle' : 'bi-hourglass-split')} me-1"></i>
                                    ${statusText}
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                                    <i class="bi bi-people me-1"></i>
                                    ${data.visitor_count} visiteur(s)
                                </span>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Date de visite :</span>
                                    <span class="fw-medium">${data.visit_date}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Créneau :</span>
                                    <span class="fw-medium">${data.time_slot}</span>
                                </div>
                            </div>
                            <div class="alert alert-light mt-3 mb-0">
                                <h6 class="alert-heading mb-2">Client</h6>
                                <p class="mb-1">
                                    <i class="bi bi-person me-1"></i> ${data.user_name}
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-envelope me-1"></i> ${data.user_email}
                                </p>
                                ${data.user_phone ? `<p class="mb-0">
                                    <i class="bi bi-telephone me-1"></i> ${data.user_phone}
                                </p>` : ''}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('eventModalBody').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('eventModalBody').innerHTML = `
                        <div class="alert alert-danger">
                            Une erreur est survenue lors du chargement des détails de la réservation.
                        </div>
                    `;
                });
            
            // Show the modal
            var modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
            
            // Prevent default browser navigation
            info.jsEvent.preventDefault();
        },
        dateClick: function(info) {
            selectedDate = info.date;
            updateEventList(info.date);
            
            // Format the date for display
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('selected-date').textContent = info.date.toLocaleDateString('fr-FR', options);
        },
        datesSet: function(info) {
            // This ensures the event list updates when changing months
            if (!selectedDate) return;
            
            // Check if the selected date is in the current view
            var start = calendar.view.activeStart;
            var end = calendar.view.activeEnd;
            
            if (selectedDate >= start && selectedDate <= end) {
                updateEventList(selectedDate);
            } else {
                // If not, update to the first day of the current view
                selectedDate = start;
                updateEventList(selectedDate);
                document.getElementById('selected-date').textContent = start.toLocaleDateString('fr-FR', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
        },
        eventDidMount: function(info) {
            // Add custom class based on status
            var status = info.event.extendedProps.status;
            if (status) {
                info.el.classList.add('fc-event-' + status);
            }
            
            // Add tooltip
            if (info.event.extendedProps.tooltip) {
                new bootstrap.Tooltip(info.el, {
                    title: info.event.extendedProps.tooltip,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        }
    });
    
    // Render the calendar
    calendar.render();
    
    // Initial load of events for today
    updateEventList(selectedDate);
    
    // Function to update the event list for a specific date
    function updateEventList(date) {
        var formattedDate = date.toISOString().split('T')[0];
        
        // Show loading state
        document.getElementById('event-list').innerHTML = `
            <div class="text-center my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        `;
        
        // Fetch events for the selected date
        fetch(`/site-manager/calendar/events?date=${formattedDate}`)
            .then(response => response.json())
            .then(data => {
                var eventList = document.getElementById('event-list');
                
                if (data.length === 0) {
                    eventList.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Aucune réservation pour cette date</p>
                        </div>
                    `;
                    return;
                }
                
                var html = '';
                
                // Group events by time slot
                var timeSlots = {};
                
                data.forEach(event => {
                    if (!timeSlots[event.time_slot]) {
                        timeSlots[event.time_slot] = [];
                    }
                    timeSlots[event.time_slot].push(event);
                });
                
                // Generate HTML for each time slot
                for (var timeSlot in timeSlots) {
                    html += `<div class="mb-3">
                        <h6 class="text-muted mb-2">${timeSlot}</h6>`;
                    
                    timeSlots[timeSlot].forEach(event => {
                        var statusClass = '';
                        var statusIcon = '';
                        
                        switch(event.status) {
                            case 'pending':
                                statusClass = 'pending';
                                statusIcon = 'hourglass-split';
                                break;
                            case 'confirmed':
                                statusClass = 'confirmed';
                                statusIcon = 'check-circle';
                                break;
                            case 'completed':
                                statusClass = 'completed';
                                statusIcon = 'check2-all';
                                break;
                            case 'cancelled':
                                statusClass = 'cancelled';
                                statusIcon = 'x-circle';
                                break;
                        }
                        
                        html += `
                            <div class="event-item ${statusClass} mb-2" onclick="window.location.href='/site-manager/bookings/${event.id}'" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="event-title">${event.site_name}</div>
                                        <div class="event-site">${event.visitor_count} visiteur(s)</div>
                                    </div>
                                    <span class="badge bg-${event.status === 'confirmed' ? 'success' : (event.status === 'pending' ? 'warning' : (event.status === 'cancelled' ? 'danger' : 'secondary'))} bg-opacity-10 text-${event.status === 'confirmed' ? 'success' : (event.status === 'pending' ? 'warning' : (event.status === 'cancelled' ? 'danger' : 'secondary'))} p-2">
                                        <i class="bi bi-${statusIcon} me-1"></i>
                                        ${event.status.charAt(0).toUpperCase() + event.status.slice(1)}
                                    </span>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                }
                
                eventList.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('event-list').innerHTML = `
                    <div class="alert alert-danger">
                        Une erreur est survenue lors du chargement des réservations.
                    </div>
                `;
            });
    }
    
    // Expose the updateEventList function to the global scope
    window.updateEventList = updateEventList;
});
</script>
@endpush
