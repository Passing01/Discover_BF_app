<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SiteManagerNotificationController extends Controller
{
    /**
     * Afficher la page des paramètres de notification
     */
    public function index()
    {
        $user = Auth::user();
        $notificationPreferences = $user->notificationPreferences ?? [
            'email' => [
                'booking_created' => true,
                'booking_updated' => true,
                'booking_cancelled' => true,
                'new_review' => true,
                'monthly_report' => true,
            ],
            'sms' => [
                'booking_created' => false,
                'booking_updated' => false,
                'booking_cancelled' => true,
                'new_review' => false,
            ],
            'push' => [
                'booking_created' => true,
                'booking_updated' => true,
                'booking_cancelled' => true,
                'new_review' => true,
            ]
        ];

        return view('site-manager.profile.notifications', compact('notificationPreferences'));
    }

    /**
     * Mettre à jour les préférences de notification
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'notifications' => 'required|array',
            'notifications.email' => 'array',
            'notifications.email.*' => 'boolean',
            'notifications.sms' => 'array',
            'notifications.sms.*' => 'boolean',
            'notifications.push' => 'array',
            'notifications.push.*' => 'boolean',
        ]);

        // Fusionner avec les préférences existantes pour conserver les valeurs non modifiées
        $currentPreferences = $user->notificationPreferences ?? [
            'email' => [],
            'sms' => [],
            'push' => []
        ];
        
        $updatedPreferences = [
            'email' => array_merge($currentPreferences['email'] ?? [], $validated['notifications']['email'] ?? []),
            'sms' => array_merge($currentPreferences['sms'] ?? [], $validated['notifications']['sms'] ?? []),
            'push' => array_merge($currentPreferences['push'] ?? [], $validated['notifications']['push'] ?? []),
        ];

        $user->notification_preferences = $updatedPreferences;
        $user->save();

        return redirect()->route('site-manager.notifications')
            ->with('success', 'Vos préférences de notification ont été mises à jour avec succès.');
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Marquer une notification spécifique comme lue
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Obtenir les notifications non lues
     */
    public function getUnreadNotifications()
    {
        $notifications = Auth::user()->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $this->getNotificationType($notification->type),
                    'data' => $notification->data,
                    'time' => $notification->created_at->diffForHumans(),
                    'url' => $this->getNotificationUrl($notification)
                ];
            });
            
        return response()->json($notifications);
    }

    /**
     * Obtenir le type de notification lisible
     */
    private function getNotificationType($type)
    {
        $types = [
            'App\Notifications\BookingCreated' => 'Nouvelle réservation',
            'App\Notifications\BookingUpdated' => 'Réservation mise à jour',
            'App\Notifications\BookingCancelled' => 'Réservation annulée',
            'App\Notifications\NewReview' => 'Nouvel avis',
            'App\Notifications\PaymentReceived' => 'Paiement reçu',
            'App\Notifications\SiteVerification' => 'Vérification du site',
        ];
        
        return $types[$type] ?? 'Notification';
    }
    
    /**
     * Obtenir l'URL de la notification
     */
    private function getNotificationUrl($notification)
    {
        $data = $notification->data;
        
        switch ($notification->type) {
            case 'App\Notifications\BookingCreated':
            case 'App\Notifications\BookingUpdated':
            case 'App\Notifications\BookingCancelled':
                return route('site-manager.bookings.show', $data['booking_id']);
                
            case 'App\Notifications\NewReview':
                return route('site-manager.sites.reviews.show', $data['review_id']);
                
            case 'App\Notifications\PaymentReceived':
                return route('site-manager.payments.show', $data['payment_id']);
                
            case 'App\Notifications\SiteVerification':
                return route('site-manager.sites.edit', $data['site_id']);
                
            default:
                return '#';
        }
    }
}
