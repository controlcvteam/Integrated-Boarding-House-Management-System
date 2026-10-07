<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the tenant portal.');
        }

        if (Auth::user()->role !== 'tenant') {
            abort(403, 'Unauthorized access. Tenant privileges required.');
        }

        return $next($request);
    }
}
