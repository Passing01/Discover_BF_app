<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTourist
{
    /**
     * Handle an incoming request.
     * Only allow users whose role is null or 'tourist'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $role = Auth::user()->role ?? 'tourist';
        if (!in_array($role, [null, 'tourist'], true)) {
            abort(403, __('Seuls les touristes peuvent accéder à cette fonctionnalité.'));
        }

        return $next($request);
    }
}
