<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Création de toutes les permissions nécessaires
        $allPermissions = [
            // Permissions de base
            'view posts', 'create posts', 'edit posts', 'delete posts',
            'manage posts', 'manage users', 'manage settings',
            
            // Permissions pour les guides
            'manage own tours', 'view bookings', 'update booking status',
            
            // Permissions pour les organisateurs d'événements
            'manage own events', 'manage event bookings', 'update event status',
            
            // Permissions pour les hôteliers
            'manage own hotel', 'manage rooms', 'manage room bookings', 'update booking status',
            
            // Permissions pour les gestionnaires de restaurants
            'manage own restaurant', 'manage menu', 'manage reservations', 'update reservation status',
            
            // Permissions pour les gestionnaires de sites
            'manage own site', 'manage site information', 'manage site bookings', 'update booking status',
            
            // Permissions pour les gestionnaires de vols
            'manage own flights', 'manage flight schedules', 'manage flight bookings', 'update flight status',
            
            // Permissions pour les gestionnaires de bus
            'manage own bus routes', 'manage bus schedules', 'manage bus bookings', 'update booking status',
            
            // Permissions pour les utilisateurs réguliers
            'book services', 'manage own bookings', 'write reviews',
            'edit own posts', 'delete own posts',
            
            // Permissions administrateur globales
            'manage all restaurants', 'manage all sites', 'manage all flights', 'manage all bus routes'
        ];

        // Créer toutes les permissions
        foreach ($allPermissions as $permissionName) {
            Permission::updateOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
        }

        // Création des rôles avec les mêmes noms que dans NonTouristUsersSeeder
        $roles = [
            'admin' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage posts', 'manage users', 'manage settings',
                'manage all restaurants', 'manage all sites', 'manage all flights', 'manage all bus routes'
            ],
            'tourist' => [
                'view posts', 'create posts', 'edit own posts', 'delete own posts',
                'book services', 'manage own bookings', 'write reviews'
            ],
            'guide' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage own tours', 'view bookings', 'update booking status'
            ],
            'event_organizer' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage own events', 'manage event bookings', 'update event status'
            ],
            'hotel_manager' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage own hotel', 'manage rooms', 'manage room bookings', 'update booking status'
            ],
            'restaurant_manager' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage own restaurant', 'manage menu', 'manage reservations', 'update reservation status'
            ],
            'site_manager' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage own site', 'manage site information', 'manage site bookings', 'update booking status'
            ],
            'vol_manager' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage own flights', 'manage flight schedules', 'manage flight bookings', 'update flight status'
            ],
            'bus_manager' => [
                'view posts', 'create posts', 'edit posts', 'delete posts',
                'manage own bus routes', 'manage bus schedules', 'manage bus bookings', 'update booking status'
            ],
            'user' => [
                'view posts', 'create posts', 'edit own posts', 'delete own posts',
                'book services', 'manage own bookings', 'write reviews'
            ]
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                ['id' => (string) Str::uuid(), 'guard_name' => 'web']
            );
            $role->syncPermissions($permissions);
        }

        // Mettre à jour les utilisateurs existants avec les rôles Spatie
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role && !$user->hasRole($user->role)) {
                $user->assignRole($user->role);
            }
        }
    }
}
