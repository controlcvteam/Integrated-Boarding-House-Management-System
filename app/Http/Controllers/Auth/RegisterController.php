<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Room;
use App\Models\RoomRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('login');
        }

        $availableRooms = Room::with(['images', 'activeTenants'])
            ->where('manual_available', true)
            ->get()
            ->filter(function ($room) {
                return $room->is_available;
            });

        $selectedRoomId = $request->query('room_id');

        return view('auth.register', compact('availableRooms', 'selectedRoomId'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'contact_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'preferred_room_id' => ['nullable', 'exists:rooms,id'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($validated) {
            // Strictly enforce tenant role and pending status
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'tenant',
                'account_status' => 'pending',
                'rejection_reason' => null,
            ]);

            $nextId = ((int) Tenant::max('id')) + 1;
            do {
                $tenantCode = 'TEN-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                $nextId++;
            } while (Tenant::where('tenant_code', $tenantCode)->exists());

            $tenant = Tenant::create([
                'user_id' => $user->id,
                'room_id' => null, // Final assignment made only by Admin
                'tenant_code' => $tenantCode,
                'full_name' => $validated['name'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'nationality' => 'Filipino',
                'status' => 'pending',
            ]);

            // If a preferred room was chosen and is available, create a pending room request
            if (!empty($validated['preferred_room_id'])) {
                $room = Room::find($validated['preferred_room_id']);
                if ($room && $room->is_available) {
                    RoomRequest::create([
                        'tenant_id' => $tenant->id,
                        'room_id' => $room->id,
                        'status' => 'pending',
                    ]);
                }
            }

            // Create notification for admin
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                AppNotification::create([
                    'user_id' => $admin->id,
                    'title' => 'New Tenant Registration Awaiting Approval',
                    'message' => $tenant->full_name . ' has registered as a new tenant and is awaiting your review.',
                    'type' => 'registration',
                    'link' => route('admin.pending-tenants.show', $tenant->id),
                    'is_read' => false,
                ]);
            }

            return $user;
        });

        Auth::login($user);

        return redirect()->route('account.pending')
            ->with('success', 'REGISTRATION SUCCESSFUL! Your account is pending approval from the Admin.');
    }
}
