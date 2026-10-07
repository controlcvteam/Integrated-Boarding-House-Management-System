<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendingTenantController extends Controller
{
    public function index(Request $request)
    {
        // Auto-heal any orphaned pending tenant user records
        $orphanedUsers = User::where('role', 'tenant')
            ->where('account_status', 'pending')
            ->whereDoesntHave('tenant')
            ->get();

        foreach ($orphanedUsers as $orphan) {
            $nextId = ((int) Tenant::max('id')) + 1;
            do {
                $tenantCode = 'TEN-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                $nextId++;
            } while (Tenant::where('tenant_code', $tenantCode)->exists());

            Tenant::create([
                'user_id' => $orphan->id,
                'tenant_code' => $tenantCode,
                'full_name' => $orphan->name,
                'contact_number' => $orphan->contact_number ?? '-',
                'address' => $orphan->address ?? '-',
                'nationality' => 'Filipino',
                'status' => 'pending',
            ]);
        }

        $query = Tenant::with(['user', 'room', 'activeRoomRequest.room'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'tenant');
            });

        // Filter by approval status
        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('account_status', $request->status);
            });
        } else {
            // Default show pending accounts
            $query->whereHas('user', function ($q) {
                $q->where('account_status', 'pending');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%");
                  });
            });
        }

        $tenants = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pending-tenants.index', compact('tenants'));
    }

    public function show($id)
    {
        $tenant = $id instanceof Tenant ? $id : Tenant::findOrFail($id);
        $tenant->load(['user', 'room.activeTenants', 'activeRoomRequest.room.activeTenants']);

        // Load all available rooms for room assignment dropdown
        $availableRooms = Room::where('manual_available', true)->get()->filter(function ($room) {
            return $room->is_available;
        });

        return view('admin.pending-tenants.show', compact('tenant', 'availableRooms'));
    }

    public function approve(Request $request, $id)
    {
        $tenant = $id instanceof Tenant ? $id : Tenant::findOrFail($id);
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'move_in_date' => ['required', 'date'],
        ]);

        $room = Room::with('activeTenants')->findOrFail($validated['room_id']);

        // Backend recheck capacity and availability (Section 30 & 31)
        if (!$room->manual_available) {
            return back()->with('error', 'Cannot assign Room ' . $room->room_number . ' because it is manually closed for maintenance/reserved.');
        }

        if ($room->activeTenants->count() >= $room->capacity) {
            return back()->with('error', 'Room ' . $room->room_number . ' is full (capacity reached: ' . $room->capacity . '). Please select another room.');
        }

        DB::transaction(function () use ($tenant, $room, $validated) {
            $oldRoomId = $tenant->room_id;

            // 1. Update user account status
            $tenant->user->update([
                'account_status' => 'approved',
                'rejection_reason' => null,
            ]);

            // 2. Update tenant status and assignment
            $tenant->update([
                'room_id' => $room->id,
                'move_in_date' => $validated['move_in_date'],
                'status' => 'active',
            ]);

            // If tenant vacated a previous room, check if old room is empty and make it available
            if ($oldRoomId && $oldRoomId != $room->id) {
                $oldRoom = Room::find($oldRoomId);
                if ($oldRoom && $oldRoom->activeTenants()->count() === 0) {
                    $oldRoom->update(['manual_available' => true]);
                }
            }

            // 3. Update room request if pending
            if ($activeRequest = $tenant->activeRoomRequest) {
                $activeRequest->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'admin_remarks' => 'Approved and assigned to Room ' . $room->room_number,
                ]);
            }

            // 4. Send notification to tenant
            AppNotification::create([
                'user_id' => $tenant->user_id,
                'title' => 'Your Account Has Been Approved!',
                'message' => 'Welcome to the boarding house! Your account has been approved and assigned to Room ' . $room->room_number . '. Move-in date: ' . Carbon::parse($validated['move_in_date'])->format('M d, Y') . '.',
                'type' => 'success',
                'link' => route('tenant.dashboard'),
                'is_read' => false,
            ]);
        });

        return redirect()->route('admin.tenants.show', $tenant->id)
            ->with('success', 'Tenant account for ' . $tenant->full_name . ' approved and assigned to Room ' . $room->room_number . ' successfully!');
    }

    public function reject(Request $request, $id)
    {
        $tenant = $id instanceof Tenant ? $id : Tenant::findOrFail($id);
        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = $validated['rejection_reason'] ?: 'Registration information could not be verified or room not available.';

        DB::transaction(function () use ($tenant, $reason) {
            $tenant->user->update([
                'account_status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            $tenant->update([
                'status' => 'inactive',
            ]);

            if ($activeRequest = $tenant->activeRoomRequest) {
                $activeRequest->update([
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'admin_remarks' => $reason,
                ]);
            }

            AppNotification::create([
                'user_id' => $tenant->user_id,
                'title' => 'Registration Application Update',
                'message' => 'Your account registration was not approved. Reason: ' . $reason,
                'type' => 'danger',
                'link' => route('account.rejected'),
                'is_read' => false,
            ]);
        });

        return redirect()->route('admin.pending-tenants.index')
            ->with('success', 'Tenant registration for ' . $tenant->full_name . ' has been rejected.');
    }
}
