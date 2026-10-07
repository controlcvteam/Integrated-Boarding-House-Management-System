<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = (int) ($request->get('billing_month', now()->month));
        $currentYear = (int) ($request->get('billing_year', now()->year));

        $query = Tenant::with(['room', 'user', 'payments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('tenant_code', 'like', "%{$search}%")
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Fetch matching tenant records and compute their payment status for current billing cycle
        $allTenants = $query->get()->map(function ($t) use ($currentMonth, $currentYear) {
            $t->current_rent_info = $t->getRentStatusForMonthYear($currentMonth, $currentYear);
            $t->current_payment_category = $t->getPaymentStatusCategory($currentMonth, $currentYear);
            return $t;
        });

        // Compute counts based on current list
        $paymentCounts = [
            'all' => $allTenants->count(),
            'to_pay' => $allTenants->where('current_payment_category', 'to_pay')->count(),
            'paid' => $allTenants->where('current_payment_category', 'paid')->count(),
            'pending' => $allTenants->where('current_payment_category', 'pending')->count(),
            'partial' => $allTenants->where('current_payment_category', 'partial')->count(),
            'rejected' => $allTenants->where('current_payment_category', 'rejected')->count(),
        ];

        // Filter by payment_status if requested
        $filteredTenants = $allTenants;
        if ($request->filled('payment_status')) {
            $statusFilter = strtolower($request->payment_status);
            if (in_array($statusFilter, ['who_pay', 'to_pay', 'unpaid', 'due'])) {
                $statusFilter = 'to_pay';
            } elseif ($statusFilter === 'reject') {
                $statusFilter = 'rejected';
            }

            if ($statusFilter !== 'all') {
                $filteredTenants = $filteredTenants->filter(function ($t) use ($statusFilter) {
                    return $t->current_payment_category === $statusFilter;
                });
            }
        }

        // Arrange / Sort tenants: default to payment_status priority
        $arrange = $request->get('arrange', 'payment_status');
        if ($arrange === 'payment_status') {
            // Priority: to_pay (Who Pay) -> pending -> partial -> rejected -> paid -> none
            $priority = [
                'to_pay' => 1,
                'pending' => 2,
                'partial' => 3,
                'rejected' => 4,
                'paid' => 5,
                'none' => 6,
            ];
            $filteredTenants = $filteredTenants->sortBy(function ($t) use ($priority) {
                return [
                    $priority[$t->current_payment_category] ?? 99,
                    $t->full_name,
                ];
            });
        } elseif ($arrange === 'name_desc') {
            $filteredTenants = $filteredTenants->sortByDesc('full_name');
        } else {
            $filteredTenants = $filteredTenants->sortBy('full_name');
        }

        // LengthAwarePaginator
        $perPage = 10;
        $page = (int) $request->get('page', 1);
        $tenants = new LengthAwarePaginator(
            $filteredTenants->forPage($page, $perPage)->values(),
            $filteredTenants->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $rooms = Room::orderBy('room_number')->get();

        return view('admin.tenants.index', compact(
            'tenants', 
            'rooms', 
            'paymentCounts', 
            'currentMonth', 
            'currentYear', 
            'arrange'
        ));
    }

    public function create()
    {
        $availableRooms = Room::where('manual_available', true)->get()->filter(function ($room) {
            return $room->is_available;
        });

        return view('admin.tenants.create', compact('availableRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'contact_number' => ['required', 'string', 'max:20'],
            'room_id' => ['required', 'exists:rooms,id'],
            'gender' => ['required', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'move_in_date' => ['required', 'date'],
            'address' => ['required', 'string'],
            'status' => ['required', 'string', 'in:active,inactive,moved_out'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $room = Room::with('activeTenants')->findOrFail($validated['room_id']);

        // Capacity and availability check
        if (!$room->manual_available) {
            return back()->withInput()->with('error', 'Selected room is marked as Not Available.');
        }

        if ($room->activeTenants->count() >= $room->capacity) {
            return back()->withInput()->with('error', 'Selected room is already at full capacity (' . $room->capacity . ' persons).');
        }

        $tenant = DB::transaction(function () use ($validated, $room) {
            $password = $validated['password'] ?: 'password';

            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'role' => 'tenant',
                'account_status' => 'approved', // Admin-created accounts are approved immediately
            ]);

            $nextId = ((int) Tenant::max('id')) + 1;
            do {
                $tenantCode = 'TEN-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                $nextId++;
            } while (Tenant::where('tenant_code', $tenantCode)->exists());

            $tenant = Tenant::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'tenant_code' => $tenantCode,
                'full_name' => $validated['full_name'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'nationality' => $validated['nationality'] ?? 'Filipino',
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'move_in_date' => $validated['move_in_date'],
                'status' => $validated['status'],
            ]);

            AppNotification::create([
                'user_id' => $user->id,
                'title' => 'Account Created by Admin',
                'message' => 'Your tenant account has been registered and assigned to Room ' . $room->room_number . '.',
                'type' => 'success',
                'link' => route('tenant.dashboard'),
                'is_read' => false,
            ]);

            return $tenant;
        });

        return redirect()->route('admin.tenants.show', $tenant->id)
            ->with('success', 'Tenant ' . $tenant->full_name . ' registered successfully!');
    }

    public function show($id)
    {
        $tenant = $id instanceof Tenant ? $id : Tenant::findOrFail($id);
        $tenant->load(['user', 'room.images', 'payments.receiver', 'maintenanceRequests']);
        
        $availableRooms = Room::where('manual_available', true)->get()->filter(function ($room) use ($tenant) {
            return $room->is_available || $room->id === $tenant->room_id;
        });

        return view('admin.tenants.show', compact('tenant', 'availableRooms'));
    }

    public function edit($id)
    {
        $tenant = ($id instanceof Tenant && $id->exists) ? $id : Tenant::findOrFail($id);
        $tenant->load(['user', 'room']);
        
        $availableRooms = Room::where('manual_available', true)->get()->filter(function ($room) use ($tenant) {
            return $room->is_available || $room->id === $tenant->room_id;
        });

        return view('admin.tenants.edit', compact('tenant', 'availableRooms'));
    }

    public function update(Request $request, $id)
    {
        $tenant = ($id instanceof Tenant && $id->exists) ? $id : Tenant::findOrFail($id);
        $tenant->load('user');

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'move_in_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive,moved_out,pending'],
        ]);

        // Room transfer handling if room changed
        $oldRoomId = $tenant->room_id;
        $newRoomId = !empty($validated['room_id']) ? (int)$validated['room_id'] : (array_key_exists('room_id', $validated) ? null : $oldRoomId);
        if ($newRoomId && $newRoomId !== $oldRoomId) {
            $newRoom = Room::with('activeTenants')->findOrFail($newRoomId);
            if (!$newRoom->manual_available) {
                return back()->withInput()->with('error', 'Room ' . $newRoom->room_number . ' is marked as Not Available.');
            }
            if ($newRoom->activeTenants->count() >= $newRoom->capacity) {
                return back()->withInput()->with('error', 'Cannot assign Room ' . $newRoom->room_number . ': room is at full capacity (' . $newRoom->capacity . ' tenants).');
            }
        }

        // Update linked user name
        if ($tenant->user) {
            $tenant->user->update([
                'name' => $validated['full_name'],
            ]);
        }

        $tenant->update([
            'full_name' => $validated['full_name'],
            'contact_number' => $validated['contact_number'],
            'room_id' => $newRoomId,
            'move_in_date' => $validated['move_in_date'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.tenants.show', $tenant->id)
            ->with('success', 'Tenant information for ' . $tenant->full_name . ' has been updated successfully!');
    }

    public function destroy($id)
    {
        $tenant = $id instanceof Tenant ? $id : Tenant::findOrFail($id);

        // Strict requirement: Dili maka delete og tenant samtang Active pa.
        if ($tenant->status === 'active') {
            return back()->with('error', 'Dili maka-delete og tenant samtang Active pa. Palihug i-click una ang "Move Out" ayha i-delete.');
        }

        $name = $tenant->full_name;

        DB::transaction(function () use ($tenant) {
            if ($tenant->user) {
                $tenant->user->delete();
            } else {
                $tenant->delete();
            }
        });

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant record for ' . $name . ' has been permanently removed.');
    }

    public function transferRoom(Request $request, $id)
    {
        $tenant = $id instanceof Tenant ? $id : Tenant::findOrFail($id);
        $validated = $request->validate([
            'new_room_id' => ['required', 'exists:rooms,id', 'different:current_room_id'],
        ]);

        $newRoom = Room::with('activeTenants')->findOrFail($validated['new_room_id']);

        if (!$newRoom->manual_available) {
            return back()->with('error', 'Selected room is currently marked as Not Available.');
        }

        if ($newRoom->activeTenants->count() >= $newRoom->capacity) {
            return back()->with('error', 'Cannot transfer: Room ' . $newRoom->room_number . ' is full (' . $newRoom->capacity . ' persons).');
        }

        $oldRoomId = $tenant->room_id;
        $oldRoomNumber = $tenant->room ? $tenant->room->room_number : 'None';
        $tenant->update(['room_id' => $newRoom->id]);

        // If vacated old room is now empty, mark available
        if ($oldRoomId && $oldRoomId != $newRoom->id) {
            $oldRoom = Room::find($oldRoomId);
            if ($oldRoom && $oldRoom->activeTenants()->count() === 0) {
                $oldRoom->update(['manual_available' => true]);
            }
        }

        AppNotification::create([
            'user_id' => $tenant->user_id,
            'title' => 'Room Transfer Notice',
            'message' => 'You have been transferred from Room ' . $oldRoomNumber . ' to Room ' . $newRoom->room_number . '. New monthly rent: ₱' . number_format($newRoom->monthly_rent, 2) . '.',
            'type' => 'info',
            'link' => route('tenant.my-room'),
            'is_read' => false,
        ]);

        return back()->with('success', 'Tenant successfully transferred to Room ' . $newRoom->room_number . '.');
    }

    public function markMovedOut(Request $request, $id)
    {
        $tenant = $id instanceof Tenant ? $id : Tenant::findOrFail($id);
        $validated = $request->validate([
            'move_out_date' => ['nullable', 'date'],
        ]);

        $moveOutDate = $validated['move_out_date'] ?? now()->toDateString();
        $oldRoomId = $tenant->room_id;

        $tenant->update([
            'status' => 'moved_out',
            'move_out_date' => $moveOutDate,
        ]);

        // If room is now empty, mark available
        if ($oldRoomId) {
            $oldRoom = Room::find($oldRoomId);
            if ($oldRoom && $oldRoom->activeTenants()->count() === 0) {
                $oldRoom->update(['manual_available' => true]);
            }
        }

        return back()->with('success', $tenant->full_name . ' has been marked as Moved Out. You can now delete this tenant record if needed.');
    }
}
