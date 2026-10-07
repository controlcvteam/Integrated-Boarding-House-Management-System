<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Room;
use App\Models\RoomRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $floor = $request->query('floor');
        $type = $request->query('type');
        $availability = $request->query('availability');

        $query = Room::with(['images', 'activeTenants'])
            ->withCount('activeTenants')
            ->searchRelated($search)
            ->when($floor, function ($q, $floor) {
                $q->where(function ($sub) use ($floor) {
                    $sub->where('floor', $floor)
                        ->orWhere('floor', 'like', "%{$floor}%");
                });
            })
            ->when($type, function ($q, $type) {
                $q->where('room_type', $type);
            })
            ->when($availability, function ($q, $avail) {
                if ($avail === 'available') {
                    $q->where('manual_available', true)
                      ->whereRaw('(SELECT COUNT(*) FROM tenants WHERE tenants.room_id = rooms.id AND tenants.status = "active") < rooms.capacity');
                } elseif ($avail === 'occupied') {
                    $q->where(function ($occQ) {
                        $occQ->where('manual_available', false)
                             ->orWhereRaw('(SELECT COUNT(*) FROM tenants WHERE tenants.room_id = rooms.id AND tenants.status = "active") >= rooms.capacity');
                    });
                }
            })
            ->orderBy('room_number');

        $rooms = $query->paginate(9)->withQueryString();

        $floors = Room::distinct()->pluck('floor')->sort();
        $types = Room::distinct()->pluck('room_type')->filter();

        // Get tenant's current pending request if any
        $tenantUser = auth()->user();
        $activeRequest = RoomRequest::where('user_id', $tenantUser->id)
            ->where('status', 'pending')
            ->first();

        return view('tenant.rooms.index', compact('rooms', 'floors', 'types', 'activeRequest'));
    }

    public function show($id)
    {
        // Notice: We specifically load images, but DO NOT expose occupants' personal data to other tenants
        $room = Room::with('images')->findOrFail($id);

        $tenantUser = auth()->user();
        $activeRequest = RoomRequest::where('user_id', $tenantUser->id)
            ->where('status', 'pending')
            ->first();

        $currentTenant = $tenantUser->tenant;
        $isMyRoom = $currentTenant && $currentTenant->room_id === $room->id;

        return view('tenant.rooms.show', compact('room', 'activeRequest', 'isMyRoom'));
    }

    public function requestRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $user = auth()->user();

        // Check if there is already a pending room request
        $existing = RoomRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have a pending room request for Room ' . ($existing->room->room_number ?? '') . '. Please wait for the Admin to review it.');
        }

        $validated = $request->validate([
            'preferred_move_in_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        $roomRequest = RoomRequest::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'preferred_move_in_date' => $validated['preferred_move_in_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Notify Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            AppNotification::send(
                $admin->id,
                'New Room Request',
                "Tenant {$user->name} has requested Room {$room->room_number}.",
                route('admin.room-requests.index'),
                'room'
            );
        }

        return redirect()->route('tenant.rooms.show', $room->id)
            ->with('success', 'Your request for Room ' . $room->room_number . ' has been submitted to the Admin for review.');
    }
}
