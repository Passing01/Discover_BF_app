@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Préférences de notification</h1>
        <p class="text-muted mb-0">Personnalisez la façon dont vous recevez les notifications</p>
    </div>
    <div>
        <a href="{{ route('site-manager.profile.edit') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour au profil
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('site-manager.notifications.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <ul class="nav nav-tabs mb-4" id="notificationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab" aria-controls="email" aria-selected="true">
                                <i class="bi bi-envelope me-1"></i> Email
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms" type="button" role="tab" aria-controls="sms" aria-selected="false">
                                <i class="bi bi-chat-dots me-1"></i> SMS
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="push-tab" data-bs-toggle="tab" data-bs-target="#push" type="button" role="tab" aria-controls="push" aria-selected="false">
                                <i class="bi bi-bell me-1"></i> Notifications push
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="notificationTabsContent">
                        <!-- Email Notifications -->
                        <div class="tab-pane fade show active" id="email" role="tabpanel" aria-labelledby="email-tab">
                            <h6 class="mb-3">Notifications par email</h6>
                            <p class="text-muted small mb-4">Choisissez les types de notifications que vous souhaitez recevoir par email.</p>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_booking_created" 
                                       name="notifications[email][booking_created]" value="1" 
                                       {{ $notificationPreferences['email']['booking_created'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_booking_created">Nouvelles réservations</label>
                                <div class="form-text">Recevoir un email lorsqu'une nouvelle réservation est effectuée pour l'un de vos sites</div>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_booking_updated" 
                                       name="notifications[email][booking_updated]" value="1"
                                       {{ $notificationPreferences['email']['booking_updated'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_booking_updated">Modifications de réservation</label>
                                <div class="form-text">Recevoir un email lorsqu'une réservation est modifiée</div>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_booking_cancelled" 
                                       name="notifications[email][booking_cancelled]" value="1"
                                       {{ $notificationPreferences['email']['booking_cancelled'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_booking_cancelled">Annulations de réservation</label>
                                <div class="form-text">Recevoir un email lorsqu'une réservation est annulée</div>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_new_review" 
                                       name="notifications[email][new_review]" value="1"
                                       {{ $notificationPreferences['email']['new_review'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_new_review">Nouveaux avis</label>
                                <div class="form-text">Recevoir un email lorsqu'un visiteur laisse un avis sur l'un de vos sites</div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_monthly_report" 
                                       name="notifications[email][monthly_report]" value="1"
                                       {{ $notificationPreferences['email']['monthly_report'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_monthly_report">Rapport mensuel</label>
                                <div class="form-text">Recevoir un résumé mensuel de l'activité de vos sites</div>
                            </div>
                        </div>
                        
                        <!-- SMS Notifications -->
                        <div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i> Les notifications SMS sont actuellement disponibles uniquement pour les numéros de téléphone français.
                            </div>
                            
                            <h6 class="mb-3">Notifications par SMS</h6>
                            <p class="text-muted small mb-4">Choisissez les types de notifications que vous souhaitez recevoir par SMS.</p>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sms_booking_created" 
                                       name="notifications[sms][booking_created]" value="1"
                                       {{ $notificationPreferences['sms']['booking_created'] ?? false ? 'checked' : '' }}
                                       {{ !auth()->user()->phone ? 'disabled' : '' }}>
                                <label class="form-check-label" for="sms_booking_created">Nouvelles réservations</label>
                                <div class="form-text">Recevoir un SMS pour chaque nouvelle réservation</div>
                                @if(!auth()->user()->phone)
                                    <div class="text-warning small">
                                        <i class="bi bi-exclamation-triangle me-1"></i> 
                                        <a href="{{ route('site-manager.profile.edit') }}">Ajoutez un numéro de téléphone</a> pour activer les notifications SMS
                                    </div>
                                @endif
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sms_booking_updated" 
                                       name="notifications[sms][booking_updated]" value="1"
                                       {{ $notificationPreferences['sms']['booking_updated'] ?? false ? 'checked' : '' }}
                                       {{ !auth()->user()->phone ? 'disabled' : '' }}>
                                <label class="form-check-label" for="sms_booking_updated">Modifications de réservation</label>
                                <div class="form-text">Recevoir un SMS pour les modifications importantes de réservation</div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="sms_booking_cancelled" 
                                       name="notifications[sms][booking_cancelled]" value="1"
                                       {{ $notificationPreferences['sms']['booking_cancelled'] ?? true ? 'checked' : '' }}
                                       {{ !auth()->user()->phone ? 'disabled' : '' }}>
                                <label class="form-check-label" for="sms_booking_cancelled">Annulations de réservation</label>
                                <div class="form-text">Recevoir un SMS lorsqu'une réservation est annulée</div>
                            </div>
                        </div>
                        
                        <!-- Push Notifications -->
                        <div class="tab-pane fade" id="push" role="tabpanel" aria-labelledby="push-tab">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i> Les notifications push nécessitent votre autorisation pour être activées dans votre navigateur.
                            </div>
                            
                            <h6 class="mb-3">Notifications push</h6>
                            <p class="text-muted small mb-4">Choisissez les types de notifications push que vous souhaitez recevoir.</p>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="push_booking_created" 
                                       name="notifications[push][booking_created]" value="1"
                                       {{ $notificationPreferences['push']['booking_created'] ?? true ? 'checked' : '' }}
                                       {{ !auth()->user()->hasPushSubscription() ? 'disabled' : '' }}>
                                <label class="form-check-label" for="push_booking_created">Nouvelles réservations</label>
                                <div class="form-text">Recevoir une notification pour chaque nouvelle réservation</div>
                                @if(!auth()->user()->hasPushSubscription())
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="enablePushNotifications">
                                        <i class="bi bi-bell me-1"></i> Activer les notifications push
                                    </button>
                                @endif
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="push_booking_updated" 
                                       name="notifications[push][booking_updated]" value="1"
                                       {{ $notificationPreferences['push']['booking_updated'] ?? true ? 'checked' : '' }}
                                       {{ !auth()->user()->hasPushSubscription() ? 'disabled' : '' }}>
                                <label class="form-check-label" for="push_booking_updated">Modifications de réservation</label>
                                <div class="form-text">Recevoir une notification pour les modifications importantes</div>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="push_booking_cancelled" 
                                       name="notifications[push][booking_cancelled]" value="1"
                                       {{ $notificationPreferences['push']['booking_cancelled'] ?? true ? 'checked' : '' }}
                                       {{ !auth()->user()->hasPushSubscription() ? 'disabled' : '' }}>
                                <label class="form-check-label" for="push_booking_cancelled">Annulations de réservation</label>
                                <div class="form-text">Recevoir une notification lorsqu'une réservation est annulée</div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="push_new_review" 
                                       name="notifications[push][new_review]" value="1"
                                       {{ $notificationPreferences['push']['new_review'] ?? true ? 'checked' : '' }}
                                       {{ !auth()->user()->hasPushSubscription() ? 'disabled' : '' }}>
                                <label class="form-check-label" for="push_new_review">Nouveaux avis</label>
                                <div class="form-text">Recevoir une notification lorsqu'un visiteur laisse un avis</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-5">
                        <a href="{{ route('site-manager.profile.edit') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Enregistrer les préférences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Centre de notifications</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Notifications récentes</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="markAllAsRead">
                        <i class="bi bi-check-all me-1"></i> Tout marquer comme lu
                    </button>
                </div>
                
                <div id="notificationsList" class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    <!-- Les notifications seront chargées ici via JavaScript -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2 mb-0 text-muted">Chargement des notifications...</p>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-bell me-1"></i> Voir toutes les notifications
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Préférences de notification</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    Personnalisez comment et quand vous recevez les mises à jour concernant vos sites et réservations.
                </p>
                
                <div class="alert alert-light">
                    <h6 class="alert-heading">Conseils</h6>
                    <ul class="mb-0 ps-3">
                        <li>Activez les notifications par email pour les résumés détaillés</li>
                        <li>Utilisez les SMS pour les alertes importantes et urgentes</li>
                        <li>Les notifications push sont idéales pour les mises à jour en temps réel</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les paramètres des notifications push -->
<div class="modal fade" id="pushSettingsModal" tabindex="-1" aria-labelledby="pushSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pushSettingsModalLabel">Activer les notifications push</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pour activer les notifications push, veuillez autoriser les notifications dans votre navigateur.</p>
                <p class="mb-0">Si la fenêtre de demande d'autorisation n'apparaît pas, vérifiez les paramètres de notification de votre navigateur.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="requestPushPermission">
                    <i class="bi bi-bell me-1"></i> Autoriser les notifications
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Style pour les onglets */
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1.25rem;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom-color: #0d6efd;
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        border-bottom-color: #dee2e6;
    }
    
    /* Style pour les interrupteurs */
    .form-switch .form-check-input {
        width: 2.5em;
        margin-left: -2.7em;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
        background-position: left center;
        border-radius: 2em;
        transition: background-position 0.15s ease-in-out;
    }
    
    .form-switch .form-check-input:checked {
        background-position: right center;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
    
    /* Style pour la liste des notifications */
    .notification-item {
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }
    
    .notification-item.unread {
        background-color: #f8f9fa;
        border-left-color: #0d6efd;
    }
    
    .notification-item:hover {
        background-color: #f1f8ff;
    }
    
    .notification-time {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .notification-badge {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }
    
    /* Style pour les icônes de notification */
    .notification-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .notification-icon.booking {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
    
    .notification-icon.review {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }
    
    .notification-icon.payment {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    
    .notification-icon.alert {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    /* Style pour le défilement personnalisé */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Style pour les cartes */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
    }
    
    /* Style pour les formulaires */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Charger les notifications
        loadNotifications();
        
        // Configurer le rafraîchissement périodique des notifications
        setInterval(loadNotifications, 300000); // Toutes les 5 minutes
        
        // Gérer le clic sur "Tout marquer comme lu"
        document.getElementById('markAllAsRead')?.addEventListener('click', function() {
            fetch('{{ route("site-manager.notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger les notifications
                    loadNotifications();
                    
                    // Mettre à jour le compteur de notifications dans la navbar si nécessaire
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        badge.textContent = '0';
                        badge.classList.remove('bg-danger');
                        badge.classList.add('d-none');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
        
        // Gérer l'activation des notifications push
        document.getElementById('enablePushNotifications')?.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('pushSettingsModal'));
            modal.show();
        });
        
        document.getElementById('requestPushPermission')?.addEventListener('click', function() {
            // Demander la permission pour les notifications
            if ('Notification' in window) {
                Notification.requestPermission().then(function(permission) {
                    if (permission === 'granted') {
                        // L'utilisateur a accepté les notifications
                        subscribeToPushNotifications();
                        
                        // Fermer la modale
                        const modal = bootstrap.Modal.getInstance(document.getElementById('pushSettingsModal'));
                        modal.hide();
                        
                        // Afficher un message de succès
                        alert('Les notifications push ont été activées avec succès !');
                        
                        // Recharger la page pour mettre à jour l'interface
                        window.location.reload();
                    } else if (permission === 'denied') {
                        alert('Vous avez refusé les notifications. Vous pouvez modifier ce paramètre dans les paramètres de votre navigateur.');
                    }
                });
            } else {
                alert('Votre navigateur ne prend pas en charge les notifications push.');
            }
        });
    });
    
    // Fonction pour charger les notifications
    function loadNotifications() {
        fetch('{{ route("site-manager.notifications.unread") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(notifications => {
            const container = document.getElementById('notificationsList');
            
            if (!notifications || notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-bell-slash text-muted" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0 text-muted">Aucune notification pour le moment</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            notifications.forEach(notification => {
                const iconClass = getNotificationIconClass(notification.type);
                const timeAgo = notification.time;
                const isUnread = notification.unread;
                
                html += `
                    <a href="${notification.url || '#'}" class="list-group-item list-group-item-action p-3 notification-item ${isUnread ? 'unread' : ''}" 
                       data-id="${notification.id}" onclick="markAsRead(event, '${notification.id}')">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon ${iconClass.class}">
                                <i class="bi ${iconClass.icon}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1">${notification.title || 'Nouvelle notification'}</h6>
                                    <small class="notification-time">${timeAgo}</small>
                                </div>
                                <p class="mb-0 text-muted small">${notification.message || ''}</p>
                            </div>
                        </div>
                    </a>
                `;
            });
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur lors du chargement des notifications:', error);
            
            const container = document.getElementById('notificationsList');
            container.innerHTML = `
                <div class="alert alert-danger m-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Une erreur est survenue lors du chargement des notifications.
                </div>
            `;
        });
    }
    
    // Marquer une notification comme lue
    function markAsRead(event, notificationId) {
        // Empêcher la navigation immédiate
        event.preventDefault();
        
        // Marquer la notification comme lue
        fetch(`/site-manager/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour l'interface utilisateur
                const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                }
                
                // Mettre à jour le compteur de notifications dans la navbar si nécessaire
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    const count = parseInt(badge.textContent) - 1;
                    if (count > 0) {
                        badge.textContent = count;
                    } else {
                        badge.classList.add('d-none');
                    }
                }
                
                // Naviguer vers l'URL de la notification
                window.location.href = event.currentTarget.href;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Naviguer vers l'URL de la notification même en cas d'erreur
            window.location.href = event.currentTarget.href;
        });
    }
    
    // Obtenir la classe et l'icône appropriées pour le type de notification
    function getNotificationIconClass(type) {
        switch (type) {
            case 'booking_created':
                return { class: 'booking', icon: 'bi-calendar-plus' };
            case 'booking_updated':
                return { class: 'booking', icon: 'bi-pencil-square' };
            case 'booking_cancelled':
                return { class: 'alert', icon: 'bi-calendar-x' };
            case 'new_review':
                return { class: 'review', icon: 'bi-star' };
            case 'payment_received':
                return { class: 'payment', icon: 'bi-credit-card' };
            default:
                return { class: '', icon: 'bi-bell' };
        }
    }
    
    // Fonction pour s'abonner aux notifications push
    function subscribeToPushNotifications() {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.ready.then(registration => {
                // Demander la permission pour les notifications
                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: '{{ config('services.vapid.public_key') }}'
                });
            })
            .then(subscription => {
                // Envoyer l'abonnement au serveur
                return fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(subscription)
                });
            })
            .then(response => response.json())
            .then(data => {
                console.log('Abonnement aux notifications push réussi:', data);
            })
            .catch(error => {
                console.error('Erreur lors de l\'abonnement aux notifications push:', error);
            });
        } else {
            console.warn('Les notifications push ne sont pas supportées par ce navigateur.');
        }
    }
</script>
@endpush
