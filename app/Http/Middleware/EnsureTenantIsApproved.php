<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'tenant') {
            if ($user->account_status === 'pending') {
                return redirect()->route('account.pending');
            }

            if ($user->account_status === 'rejected') {
                return redirect()->route('account.rejected');
            }
        }

        return $next($request);
    }
}
