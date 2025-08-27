<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountFeature;
use App\Models\AccountUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountService
{
    /**
     * Create a new professional account
     */
    public function createAccount(array $data, User $owner): Account
    {
        return DB::transaction(function () use ($data, $owner) {
            // Create the account
            $account = Account::create([
                'business_name' => $data['business_name'],
                'legal_name' => $data['legal_name'],
                'tax_number' => $data['tax_number'] ?? null,
                'registration_number' => $data['registration_number'] ?? null,
                'website' => $data['website'] ?? null,
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'],
                'country' => $data['country'],
                'status' => config('accounts.default_status', 'pending'),
            ]);

            // Create account verification record
            $account->verification()->create([
                'status' => 'pending',
            ]);

            // Create billing information
            $account->billing()->create([
                'billing_name' => $data['billing_name'] ?? $data['business_name'],
                'billing_email' => $data['billing_email'] ?? $data['email'],
                'billing_phone' => $data['billing_phone'] ?? $data['phone'],
                'billing_address' => $data['billing_address'] ?? $data['address'],
                'billing_city' => $data['billing_city'] ?? $data['city'],
                'billing_state' => $data['billing_state'] ?? $data['state'] ?? null,
                'billing_postal_code' => $data['billing_postal_code'] ?? $data['postal_code'],
                'billing_country' => $data['billing_country'] ?? $data['country'],
            ]);

            // Add the owner to the account
            $this->addUserToAccount($account, $owner, 'owner');

            // If this is the user's first account, set it as current
            if (!$owner->current_account_id) {
                $owner->current_account_id = $account->id;
                $owner->save();
            }

            return $account;
        });
    }

    /**
     * Add a user to an account with a specific role
     */
    public function addUserToAccount(Account $account, User $user, string $role, array $permissions = [], bool $isPrimaryContact = false): AccountUser
    {
        // If user is being added as owner, ensure they're the only owner
        if ($role === 'owner') {
            $account->users()->wherePivot('role', 'owner')->update(['role' => 'admin']);
            $isPrimaryContact = true;
            
            // Owner gets all permissions
            $permissions = $this->getAllPermissions();
        }

        // If this is the first user, make them the primary contact if not set
        if ($account->users()->count() === 0) {
            $isPrimaryContact = true;
        }

        // If setting as primary contact, unset any existing primary contact
        if ($isPrimaryContact) {
            $account->users()->updateExistingPivot(
                $account->users()->pluck('users.id'),
                ['is_primary_contact' => false]
            );
        }

        // Add or update the user
        $account->users()->syncWithoutDetaching([
            $user->id => [
                'role' => $role,
                'permissions' => $permissions,
                'is_primary_contact' => $isPrimaryContact,
            ]
        ]);

        return $account->users()->find($user->id)->pivot;
    }

    /**
     * Activate a feature for an account
     */
    public function activateFeature(Account $account, string $feature, array $settings = []): AccountFeature
    {
        $availableFeatures = AccountFeature::getAvailableFeatures();
        
        if (!isset($availableFeatures[$feature])) {
            throw new \InvalidArgumentException("Feature {$feature} is not available");
        }

        return $account->features()->updateOrCreate(
            ['feature' => $feature],
            [
                'is_active' => true,
                'activated_at' => now(),
                'settings' => $settings,
            ]
        );
    }

    /**
     * Deactivate a feature for an account
     */
    public function deactivateFeature(Account $account, string $feature): bool
    {
        return (bool) $account->features()
            ->where('feature', $feature)
            ->update([
                'is_active' => false,
                'deactivated_at' => now(),
            ]);
    }

    /**
     * Get all available permissions
     */
    public function getAllPermissions(): array
    {
        return [
            'account:view',
            'account:update',
            'users:manage',
            'users:invite',
            'billing:view',
            'billing:update',
            'settings:manage',
            'reports:view',
            'bookings:view',
            'bookings:manage',
            'inventory:manage',
            'checkin:process',
            'content:manage',
            'analytics:view',
        ];
    }

    /**
     * Get all available features with their default settings
     */
    public function getAvailableFeatures(): array
    {
        return AccountFeature::getAvailableFeatures();
    }

    /**
     * Update account verification status
     */
    public function updateVerificationStatus(Account $account, string $status, ?string $rejectionReason = null, ?User $reviewedBy = null): void
    {
        $verification = $account->verification;
        
        if (!$verification) {
            $verification = $account->verification()->create([
                'status' => $status,
                'reviewed_by' => $reviewedBy?->id,
                'reviewed_at' => now(),
                'rejection_reason' => $rejectionReason,
            ]);
        } else {
            $verification->update([
                'status' => $status,
                'reviewed_by' => $reviewedBy?->id,
                'reviewed_at' => now(),
                'rejection_reason' => $rejectionReason,
            ]);
        }

        // If approved, update account status
        if ($status === 'approved') {
            $account->update([
                'status' => 'active',
                'verified_at' => now(),
            ]);
        }
    }

    /**
     * Get accounts where user has a specific role
     */
    public function getAccountsByUserRole(User $user, string $role): \Illuminate\Database\Eloquent\Collection
    {
        return $user->accounts()
            ->wherePivot('role', $role)
            ->get();
    }
}
