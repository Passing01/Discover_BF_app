<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Site;
use App\Models\SiteBooking;
use Carbon\Carbon;

class SiteManagerSettingsController extends Controller
{
    /**
     * Afficher la page des paramètres du tableau de bord
     */
    public function index()
    {
        $user = Auth::user();
        $dashboardSettings = $user->dashboard_settings ?? [
            'default_view' => 'overview',
            'show_recent_bookings' => true,
            'show_recent_sites' => true,
            'show_statistics' => true,
            'show_calendar' => true,
            'show_quick_actions' => true,
            'items_per_page' => 10,
            'refresh_interval' => 300, // 5 minutes
            'theme' => 'light',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'timezone' => config('app.timezone'),
            'notifications' => [
                'email' => true,
                'sms' => false,
                'push' => true
            ]
        ];

        // Récupérer les statistiques pour l'affichage
        $stats = [
            'total_sites' => Site::where('manager_id', $user->id)->count(),
            'active_sites' => Site::where('manager_id', $user->id)->where('is_active', true)->count(),
            'total_bookings' => SiteBooking::whereHas('site', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->count(),
            'pending_bookings' => SiteBooking::whereHas('site', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->where('status', 'pending')->count(),
            'monthly_revenue' => SiteBooking::whereHas('site', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->where('status', 'completed')
              ->whereMonth('created_at', now()->month)
              ->sum('total_amount'),
            'total_visitors' => SiteBooking::whereHas('site', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->where('visit_date', '>=', now()->subDays(30))
              ->sum('visitors_count')
        ];

        // Récupérer les fuseaux horaires disponibles
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $timezoneOptions = array_combine($timezones, $timezones);

        // Récupérer les vues disponibles pour le tableau de bord
        $views = [
            'overview' => 'Vue d\'ensemble',
            'bookings' => 'Réservations',
            'sites' => 'Sites',
            'calendar' => 'Calendrier',
            'reports' => 'Rapports'
        ];

        return view('site-manager.settings.dashboard', compact(
            'dashboardSettings', 
            'stats', 
            'timezoneOptions',
            'views'
        ));
    }

    /**
     * Mettre à jour les paramètres du tableau de bord
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'default_view' => 'required|in:overview,bookings,sites,calendar,reports',
            'show_recent_bookings' => 'boolean',
            'show_recent_sites' => 'boolean',
            'show_statistics' => 'boolean',
            'show_calendar' => 'boolean',
            'show_quick_actions' => 'boolean',
            'items_per_page' => 'required|integer|min:5|max:100',
            'refresh_interval' => 'required|integer|min:30|max:3600',
            'theme' => 'required|in:light,dark,system',
            'date_format' => 'required|string',
            'time_format' => 'required|in:H:i,h:i A',
            'timezone' => 'required|timezone',
            'notifications.email' => 'boolean',
            'notifications.sms' => 'boolean',
            'notifications.push' => 'boolean',
        ]);

        // Mettre à jour les paramètres du tableau de bord
        $dashboardSettings = $user->dashboard_settings ?? [];
        $dashboardSettings = array_merge($dashboardSettings, [
            'default_view' => $validated['default_view'],
            'show_recent_bookings' => $validated['show_recent_bookings'] ?? false,
            'show_recent_sites' => $validated['show_recent_sites'] ?? false,
            'show_statistics' => $validated['show_statistics'] ?? true,
            'show_calendar' => $validated['show_calendar'] ?? true,
            'show_quick_actions' => $validated['show_quick_actions'] ?? true,
            'items_per_page' => (int) $validated['items_per_page'],
            'refresh_interval' => (int) $validated['refresh_interval'],
            'theme' => $validated['theme'],
            'date_format' => $validated['date_format'],
            'time_format' => $validated['time_format'],
            'timezone' => $validated['timezone'],
            'notifications' => [
                'email' => $validated['notifications']['email'] ?? false,
                'sms' => $validated['notifications']['sms'] ?? false,
                'push' => $validated['notifications']['push'] ?? false,
            ]
        ]);

        // Mettre à jour l'utilisateur
        $user->dashboard_settings = $dashboardSettings;
        $user->timezone = $validated['timezone'];
        $user->save();

        // Mettre à jour la session avec le nouveau fuseau horaire
        session(['timezone' => $validated['timezone']]);
        
        // Mettre à jour le thème dans la session
        session(['theme' => $validated['theme']]);

        return redirect()->route('site-manager.settings.dashboard')
            ->with('success', 'Les paramètres du tableau de bord ont été mis à jour avec succès.');
    }

    /**
     * Réinitialiser les paramètres du tableau de bord aux valeurs par défaut
     */
    public function reset()
    {
        $user = Auth::user();
        
        // Réinitialiser les paramètres du tableau de bord
        $user->dashboard_settings = null;
        $user->save();

        return redirect()->route('site-manager.settings.dashboard')
            ->with('success', 'Les paramètres du tableau de bord ont été réinitialisés avec succès.');
    }

    /**
     * Mettre à jour le thème de l'interface
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,system'
        ]);

        // Mettre à jour le thème dans la session
        session(['theme' => $request->theme]);

        // Mettre à jour le thème dans les paramètres utilisateur si connecté
        if (Auth::check()) {
            $user = Auth::user();
            $dashboardSettings = $user->dashboard_settings ?? [];
            $dashboardSettings['theme'] = $request->theme;
            $user->dashboard_settings = $dashboardSettings;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mettre à jour le fuseau horaire de l'utilisateur
     */
    public function updateTimezone(Request $request)
    {
        $request->validate([
            'timezone' => 'required|timezone'
        ]);

        // Mettre à jour le fuseau horaire dans la session
        session(['timezone' => $request->timezone]);
        
        // Mettre à jour le fuseau horaire dans les paramètres utilisateur si connecté
        if (Auth::check()) {
            $user = Auth::user();
            $dashboardSettings = $user->dashboard_settings ?? [];
            $dashboardSettings['timezone'] = $request->timezone;
            $user->dashboard_settings = $dashboardSettings;
            $user->timezone = $request->timezone;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Obtenir les statistiques mises à jour via AJAX
     */
    public function getStats()
    {
        $user = Auth::user();
        
        // Utiliser le fuseau horaire de l'utilisateur
        $timezone = $user->timezone ?? config('app.timezone');
        $now = now()->setTimezone($timezone);
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        
        // Requêtes de base
        $sitesQuery = Site::where('manager_id', $user->id);
        $bookingsQuery = SiteBooking::whereHas('site', function($query) use ($user) {
            $query->where('manager_id', $user->id);
        });
        
        // Calculer les statistiques
        $stats = [
            'total_sites' => $sitesQuery->count(),
            'active_sites' => (clone $sitesQuery)->where('is_active', true)->count(),
            'total_bookings' => (clone $bookingsQuery)->count(),
            'pending_bookings' => (clone $bookingsQuery)->where('status', 'pending')->count(),
            'monthly_revenue' => (clone $bookingsQuery)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_amount'),
            'total_visitors' => (clone $bookingsQuery)
                ->where('visit_date', '>=', $thirtyDaysAgo)
                ->sum('visitors_count'),
            'updated_at' => $now->format('Y-m-d H:i:s'),
            'timezone' => $timezone
        ];
        
        // Ajouter des statistiques supplémentaires pour les graphiques
        $stats['bookings_by_status'] = (clone $bookingsQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        $stats['revenue_by_month'] = (clone $bookingsQuery)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
            ->where('status', 'completed')
            ->where('created_at', '>=', $now->copy()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->mapWithKeys(function ($total, $month) {
                return [Carbon::parse($month)->format('M Y') => (float) $total];
            });
            
        $stats['visitors_by_month'] = (clone $bookingsQuery)
            ->selectRaw('DATE_FORMAT(visit_date, "%Y-%m") as month, SUM(visitors_count) as total')
            ->where('visit_date', '>=', $now->copy()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->mapWithKeys(function ($total, $month) {
                return [Carbon::parse($month)->format('M Y') => (int) $total];
            });
        
        return response()->json($stats);
    }

    /**
     * Exporter les données du tableau de bord
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'pdf');
        $data = [];
        
        // Récupérer les données à exporter
        $data['user'] = $user;
        $data['sites'] = Site::where('manager_id', $user->id)->get();
        $data['bookings'] = SiteBooking::whereHas('site', function($query) use ($user) {
            $query->where('manager_id', $user->id);
        })->with(['site', 'user'])->latest()->take(50)->get();
        
        // Générer le rapport en fonction du format demandé
        if ($format === 'pdf') {
            $pdf = \PDF::loadView('exports.dashboard-pdf', $data);
            return $pdf->download('tableau-de-bord-' . now()->format('Y-m-d') . '.pdf');
        } elseif ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="tableau-de-bord-' . now()->format('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                
                // En-têtes des sites
                fputcsv($file, ['Sites']);
                fputcsv($file, ['ID', 'Nom', 'Statut', 'Créé le']);
                
                // Données des sites
                foreach ($data['sites'] as $site) {
                    fputcsv($file, [
                        $site->id,
                        $site->name,
                        $site->is_active ? 'Actif' : 'Inactif',
                        $site->created_at->format('d/m/Y')
                    ]);
                }
                
                // Ligne vide
                fputcsv($file, []);
                
                // En-têtes des réservations
                fputcsv($file, ['Réservations']);
                fputcsv($file, ['ID', 'Site', 'Client', 'Date de visite', 'Statut', 'Montant']);
                
                // Données des réservations
                foreach ($data['bookings'] as $booking) {
                    fputcsv($file, [
                        $booking->id,
                        $booking->site->name,
                        $booking->user->name,
                        $booking->visit_date->format('d/m/Y'),
                        ucfirst($booking->status),
                        number_format($booking->total_amount, 2, ',', ' ') . ' €'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        } else {
            return response()->json($data);
        }
    }
}
