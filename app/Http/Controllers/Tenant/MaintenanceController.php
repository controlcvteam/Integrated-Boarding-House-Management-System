<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return redirect()->route('tenant.dashboard');
        }

        $requests = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->with('room')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tenant.maintenance.index', compact('requests'));
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || !$tenant->room) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'You need an assigned room to file maintenance requests.');
        }

        return view('tenant.maintenance.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || !$tenant->room) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'You need an assigned room to file maintenance requests.');
        }

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('maintenance', 'public');
        }

        $maintenance = MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
            'status' => 'Pending',
        ]);

        // Notify Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            AppNotification::send(
                $admin->id,
                'New Maintenance Request',
                "Tenant {$tenant->user->name} reported [{$validated['category']}]: '{$validated['title']}' for Room {$tenant->room->room_number}.",
                route('admin.maintenance.show', $maintenance->id),
                'maintenance'
            );
        }

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'Your maintenance request has been submitted. The Admin will review it and dispatch assistance.');
    }

    public function show($id)
    {
        $tenant = auth()->user()->tenant;
        $request = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->with(['room', 'tenant.user'])
            ->findOrFail($id);

        return view('tenant.maintenance.show', compact('request'));
    }

    public function edit($id)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return redirect()->route('tenant.dashboard');
        }

        $request = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->with('room')
            ->findOrFail($id);

        return view('tenant.maintenance.edit', compact('request', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return redirect()->route('tenant.dashboard');
        }

        $maintenance = MaintenanceRequest::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $attachmentPath = $maintenance->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('maintenance', 'public');
        }

        $maintenance->update([
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'Your maintenance request has been updated successfully.');
    }

    public function destroy($id)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return redirect()->route('tenant.dashboard');
        }

        $maintenance = MaintenanceRequest::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($maintenance->attachment_path) {
            Storage::disk('public')->delete($maintenance->attachment_path);
        }

        $maintenance->delete();

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'Maintenance request has been deleted successfully.');
    }
}
