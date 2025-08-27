<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountFeature;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new professional account
     */
    public function create()
    {
        // If user already has accounts, redirect to account selection
        if (auth()->user()->accounts()->count() > 0) {
            return redirect()->route('account.select');
        }

        $features = $this->accountService->getAvailableFeatures();
        
        return view('account.create', [
            'features' => $features,
        ]);
    }

    /**
     * Store a newly created account in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'features' => 'nullable|array',
            'features.*' => ['string', Rule::in(array_keys($this->accountService->getAvailableFeatures()))],
        ]);

        // Create the account
        $account = $this->accountService->createAccount($validated, $request->user());

        // Activate selected features
        if (!empty($validated['features'])) {
            foreach ($validated['features'] as $feature) {
                $this->accountService->activateFeature($account, $feature);
            }
        }

        return redirect()->route('account.show', $account)
            ->with('success', 'Professional account created successfully!');
    }

    /**
     * Display the account selection page
     */
    public function select()
    {
        $accounts = auth()->user()->accounts()
            ->withCount('users')
            ->latest()
            ->get();

        return view('account.select', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * Switch to the selected account
     */
    public function switch(Account $account)
    {
        if (!auth()->user()->hasAccountAccess($account)) {
            abort(403);
        }

        auth()->user()->switchToAccount($account);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Switched to ' . $account->business_name);
    }

    /**
     * Display the specified account
     */
    public function show(Account $account)
    {
        $this->authorize('view', $account);

        $account->load(['features', 'users', 'verification']);
        
        return view('account.show', [
            'account' => $account,
            'availableFeatures' => $this->accountService->getAvailableFeatures(),
        ]);
    }

    /**
     * Show the form for editing the specified account
     */
    public function edit(Account $account)
    {
        $this->authorize('update', $account);

        return view('account.edit', [
            'account' => $account,
            'availableFeatures' => $this->accountService->getAvailableFeatures(),
        ]);
    }

    /**
     * Update the specified account in storage
     */
    public function update(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'timezone' => 'required|timezone',
            'currency' => 'required|string|size:3',
            'features' => 'nullable|array',
            'features.*' => ['string', Rule::in(array_keys($this->accountService->getAvailableFeatures()))],
        ]);

        // Update account
        $account->update($validated);

        // Update features
        $currentFeatures = $account->features()->pluck('feature')->toArray();
        $newFeatures = $validated['features'] ?? [];

        // Deactivate removed features
        foreach (array_diff($currentFeatures, $newFeatures) as $feature) {
            $this->accountService->deactivateFeature($account, $feature);
        }

        // Activate new features
        foreach (array_diff($newFeatures, $currentFeatures) as $feature) {
            $this->accountService->activateFeature($account, $feature);
        }

        return redirect()->route('account.show', $account)
            ->with('success', 'Account updated successfully!');
    }

    /**
     * Show the form for managing account users
     */
    public function users(Account $account)
    {
        $this->authorize('manageUsers', $account);

        $account->load(['users']);
        
        return view('account.users', [
            'account' => $account,
        ]);
    }

    /**
     * Invite a new user to the account
     */
    public function inviteUser(Request $request, Account $account)
    {
        $this->authorize('manageUsers', $account);

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => ['required', Rule::in(['admin', 'manager', 'staff'])],
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in($this->accountService->getAllPermissions())],
        ]);

        // In a real app, you would send an invitation email here
        // For now, we'll just create the user if they don't exist
        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['email'], // In a real app, you'd ask for a name
                'password' => bcrypt(Str::random(16)), // Generate a random password that can be reset
                'email_verified_at' => now(),
            ]
        );

        // Add user to account
        $this->accountService->addUserToAccount(
            $account,
            $user,
            $validated['role'],
            $validated['permissions'] ?? []
        );

        // TODO: Send invitation email

        return redirect()->route('account.users', $account)
            ->with('success', 'User invited successfully!');
    }

    /**
     * Remove a user from the account
     */
    public function removeUser(Account $account, User $user)
    {
        $this->authorize('manageUsers', $account);

        // Prevent removing the last owner
        if ($user->pivot->role === 'owner' && 
            $account->users()->wherePivot('role', 'owner')->count() <= 1) {
            return back()->with('error', 'Cannot remove the last owner of the account.');
        }

        $account->users()->detach($user->id);

        // If the removed user was the current account, switch to another account
        if ($user->current_account_id === $account->id) {
            $user->update(['current_account_id' => $user->accounts()->first()?->id]);
        }

        return back()->with('success', 'User removed from account.');
    }

    /**
     * Show the account settings page
     */
    public function settings(Account $account)
    {
        $this->authorize('update', $account);

        $account->load(['billing', 'verification']);
        
        return view('account.settings', [
            'account' => $account,
            'verificationStatuses' => [
                'pending' => 'En attente',
                'in_review' => 'En cours de vérification',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
            ],
        ]);
    }

    /**
     * Update the account's billing information
     */
    public function updateBilling(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'billing_cycle' => ['required', Rule::in(['monthly', 'quarterly', 'annually'])],
            'tax_identification_number' => 'nullable|string|max:100',
        ]);

        $account->billing()->update($validated);

        return back()->with('success', 'Billing information updated successfully!');
    }

    /**
     * Upload verification documents
     */
    public function uploadVerification(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'document_type' => ['required', Rule::in([
                'business_license', 'tax_certificate', 'registration_certificate', 'id_document'
            ])],
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        $path = $request->file('document')->store("verifications/{$account->id}", 'private');
        
        $account->verification()->updateOrCreate(
            ['account_id' => $account->id],
            [
                $validated['document_type'] . '_path' => $path,
                'status' => 'pending', // Reset status when new document is uploaded
            ]
        );

        return back()->with('success', 'Document uploaded successfully!');
    }

    /**
     * Submit account for verification
     */
    public function submitForVerification(Account $account)
    {
        $this->authorize('update', $account);

        $verification = $account->verification;
        
        if (!$verification) {
            return back()->with('error', 'Please upload all required documents first.');
        }

        // Check if all required documents are uploaded
        $requiredDocs = ['business_license_path', 'id_document_path'];
        $missingDocs = [];
        
        foreach ($requiredDocs as $doc) {
            if (empty($verification->$doc)) {
                $missingDocs[] = str_replace('_', ' ', ucfirst(str_replace('_path', '', $doc)));
            }
        }
        
        if (!empty($missingDocs)) {
            return back()->with('error', 'Please upload all required documents: ' . implode(', ', $missingDocs));
        }

        $verification->update([
            'status' => 'in_review',
            'reviewed_at' => null,
            'reviewed_by' => null,
            'rejection_reason' => null,
        ]);

        // TODO: Notify admin about the verification request

        return back()->with('success', 'Account submitted for verification. We will review your documents shortly.');
    }
}
