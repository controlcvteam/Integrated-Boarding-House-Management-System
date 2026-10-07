<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isTenant()) {
                if ($user->isApproved()) {
                    return redirect()->route('tenant.dashboard');
                } elseif ($user->isPending()) {
                    return redirect()->route('account.pending');
                } else {
                    return redirect()->route('account.rejected');
                }
            }
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        if ($user->isTenant()) {
            if ($user->isApproved()) {
                return redirect()->intended(route('tenant.dashboard'))
                    ->with('success', 'Welcome back, ' . $user->name . '!');
            }

            if ($user->isPending()) {
                return redirect()->route('account.pending');
            }

            if ($user->isRejected()) {
                return redirect()->route('account.rejected');
            }
        }

        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function pending()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isApproved()) {
            return redirect()->route('tenant.dashboard');
        }

        if ($user->isRejected()) {
            return redirect()->route('account.rejected');
        }

        $tenant = $user->tenant;
        $activeRequest = $tenant ? $tenant->activeRoomRequest()->with('room.primaryImage')->first() : null;

        return view('auth.pending', compact('user', 'tenant', 'activeRequest'));
    }

    public function rejected()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isApproved()) {
            return redirect()->route('tenant.dashboard');
        }

        if ($user->isPending()) {
            return redirect()->route('account.pending');
        }

        $tenant = $user->tenant;

        return view('auth.rejected', compact('user', 'tenant'));
    }
}
