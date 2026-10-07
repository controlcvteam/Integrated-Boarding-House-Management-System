<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Room;
use App\Models\RoomRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomRequest::with(['tenant.user', 'room.activeTenants', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default show pending first
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            })->orWhereHas('room', function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();
        $availableRooms = Room::where('manual_available', true)->get()->filter(function ($room) {
            return $room->is_available;
        });

        return view('admin.room-requests.index', compact('requests', 'availableRooms'));
    }

    public function approve(Request $request, $id)
    {
        $roomRequest = ($id instanceof RoomRequest && $id->exists) ? $id : RoomRequest::findOrFail($id);
        $validated = $request->validate([
            'room_id' => ['nullable', 'exists:rooms,id'],
            'move_in_date' => ['nullable', 'date'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $targetRoomId = $validated['room_id'] ?? $roomRequest->room_id;
        $room = Room::with('activeTenants')->findOrFail($targetRoomId);

        // Capacity and availability recheck
        if (!$room->manual_available) {
            return back()->with('error', 'Cannot assign Room ' . $room->room_number . ' because it is manually marked as Not Available.');
        }

        if ($room->activeTenants->count() >= $room->capacity) {
            return back()->with('error', 'Room ' . $room->room_number . ' is full (capacity: ' . $room->capacity . ').');
        }

        DB::transaction(function () use ($roomRequest, $room, $validated) {
            $tenant = $roomRequest->tenant ?: ($roomRequest->user ? $roomRequest->user->tenant : null);
            $oldRoomId = $tenant ? $tenant->room_id : null;

            $moveInDate = $validated['move_in_date'] ?? ($tenant->move_in_date ?? now());

            if ($tenant) {
                // Update tenant room and status
                $tenant->update([
                    'room_id' => $room->id,
                    'move_in_date' => $moveInDate,
                    'status' => 'active',
                ]);

                // If tenant vacated a previous room, check if the old room is empty and make it available
                if ($oldRoomId && $oldRoomId != $room->id) {
                    $oldRoom = Room::find($oldRoomId);
                    if ($oldRoom && $oldRoom->activeTenants()->count() === 0) {
                        $oldRoom->update(['manual_available' => true]);
                    }
                }

                // Update user status to approved
                if ($tenant->user) {
                    $tenant->user->update([
                        'account_status' => 'approved',
                        'rejection_reason' => null,
                    ]);
                }
            }

            // Update request
            $roomRequest->update([
                'room_id' => $room->id,
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'admin_remarks' => $validated['admin_remarks'] ?? 'Room request approved and assigned to Room ' . $room->room_number,
            ]);

            $userId = $tenant ? $tenant->user_id : $roomRequest->user_id;
            if ($userId) {
                // Send notification
                AppNotification::create([
                    'user_id' => $userId,
                    'title' => 'Room Request Approved!',
                    'message' => 'Your request for Room ' . $room->room_number . ' has been approved. Move-in date: ' . Carbon::parse($moveInDate)->format('M d, Y') . '.',
                    'type' => 'success',
                    'link' => route('tenant.my-room'),
                    'is_read' => false,
                ]);
            }
        });

        $name = $roomRequest->tenant ? $roomRequest->tenant->full_name : ($roomRequest->user->name ?? 'Applicant');
        return back()->with('success', 'Room request for ' . $name . ' approved successfully!');
    }

    public function reject(Request $request, $id)
    {
        $roomRequest = ($id instanceof RoomRequest && $id->exists) ? $id : RoomRequest::findOrFail($id);
        $validated = $request->validate([
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = $validated['admin_remarks'] ?: 'Selected room is no longer available or request could not be accommodated.';

        $roomRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_remarks' => $reason,
        ]);

        $userId = $roomRequest->tenant ? $roomRequest->tenant->user_id : $roomRequest->user_id;
        if ($userId) {
            AppNotification::create([
                'user_id' => $userId,
                'title' => 'Room Request Rejected',
                'message' => 'Your request for Room ' . ($roomRequest->room ? $roomRequest->room->room_number : '') . ' was not approved. Remarks: ' . $reason,
                'type' => 'danger',
                'link' => route('tenant.rooms.index'),
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Room request has been rejected.');
    }

    public function destroy($id)
    {
        $roomRequest = ($id instanceof RoomRequest && $id->exists) ? $id : RoomRequest::findOrFail($id);
        $name = $roomRequest->tenant ? $roomRequest->tenant->full_name : ($roomRequest->user->name ?? 'Applicant');
        $roomNumber = $roomRequest->room ? $roomRequest->room->room_number : '';

        $roomRequest->delete();

        return back()->with('success', 'Room request for ' . $name . ($roomNumber ? ' (Room ' . $roomNumber . ')' : '') . ' has been removed.');
    }
}
