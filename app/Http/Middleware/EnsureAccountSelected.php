<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountSelected
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Skip for unauthenticated users or API routes
        if (!$user || $request->is('api/*')) {
            return $next($request);
        }

        // Skip for account-related routes
        $accountRoutes = [
            'account.*',
            'register',
            'login',
            'password.*',
            'verification.*',
            'admin.*',
        ];

        foreach ($accountRoutes as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }

        // If user has no accounts, redirect to account creation
        if ($user->accounts()->count() === 0) {
            return redirect()->route('account.create')
                ->with('info', 'Please create a professional account to continue.');
        }

        // If no account is selected, redirect to account selection
        if (!$user->current_account_id) {
            return redirect()->route('account.select')
                ->with('info', 'Please select an account to continue.');
        }

        // Verify the selected account exists and user has access
        $account = $user->accounts()
            ->where('accounts.id', $user->current_account_id)
            ->first();

        if (!$account) {
            // If the current account doesn't exist or user lost access
            $user->current_account_id = null;
            $user->save();
            
            return redirect()->route('account.select')
                ->with('error', 'The selected account is no longer accessible.');
        }

        // Share the current account with all views
        view()->share('currentAccount', $account);
        
        return $next($request);
    }
}
