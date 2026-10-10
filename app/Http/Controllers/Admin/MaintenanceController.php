<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with(['tenant.user', 'room']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('request_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('full_name', 'like', "%{$search}%");
                  })->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $statusClean = strtolower(str_replace([' ', '_'], '', (string)$status));
            $variants = match($statusClean) {
                'pending' => ['pending', 'Pending'],
                'inprogress' => ['in_progress', 'In Progress', 'inprogress', 'In progress'],
                'resolved' => ['resolved', 'Resolved'],
                'rejected' => ['rejected', 'Rejected'],
                default => [$status]
            };
            $query->whereIn('status', $variants);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $requests = $query->latest()->paginate(10)->withQueryString();
        $categories = ['Plumbing', 'Electrical', 'Structural', 'Appliance', 'Carpentry', 'General', 'Other'];

        return view('admin.maintenance.index', compact('requests', 'categories'));
    }

    public function create()
    {
        return redirect()->route('admin.maintenance.index')
            ->with('info', 'Only tenants can submit maintenance requests. Admin can only update the status of existing requests.');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.maintenance.index')
            ->with('error', 'Only tenants can submit maintenance requests. Admin can only update the status of existing requests.');
    }

    public function show($id)
    {
        $maintenance = ($id instanceof MaintenanceRequest && $id->exists) ? $id : MaintenanceRequest::findOrFail($id);
        $maintenance->load(['tenant.user', 'room']);
        return view('admin.maintenance.show', compact('maintenance'));
    }

    public function edit($id)
    {
        $maintenance = ($id instanceof MaintenanceRequest && $id->exists) ? $id : MaintenanceRequest::findOrFail($id);
        $maintenance->load(['tenant', 'room']);
        $categories = ['Plumbing', 'Electrical', 'Structural', 'Appliance', 'Carpentry', 'General', 'Other'];

        return view('admin.maintenance.edit', compact('maintenance', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $maintenance = ($id instanceof MaintenanceRequest && $id->exists) ? $id : MaintenanceRequest::findOrFail($id);
        $validated = $request->validate([
            'status' => ['required', 'in:Pending,In Progress,Resolved,Rejected,pending,in_progress,resolved,rejected'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'target_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $statusClean = strtolower(str_replace([' ', '_'], '', $validated['status']));
        $statusNormalized = match($statusClean) {
            'inprogress' => 'In Progress',
            'resolved' => 'Resolved',
            'rejected' => 'Rejected',
            default => 'Pending'
        };

        $resolvedAt = ($statusNormalized === 'Resolved') 
            ? ($maintenance->resolved_at ?: now()) 
            : null;

        $remarks = $validated['remarks'] ?? null;

        $maintenance->update([
            'status' => $statusNormalized,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'remarks' => $remarks,
            'admin_remarks' => $remarks,
            'resolved_at' => $resolvedAt,
        ]);

        // Mark related pending admin notifications as read
        AppNotification::where('link', 'like', '%' . $maintenance->id . '%')
            ->where('is_read', false)
            ->whereHas('user', function ($q) {
                $q->where('role', 'admin');
            })
            ->update(['is_read' => true]);

        // Send tailored notification to tenant
        if ($maintenance->tenant && $maintenance->tenant->user_id) {
            $codeStr = $maintenance->request_code ? '#' . $maintenance->request_code : '#MNT-' . str_pad($maintenance->id, 5, '0', STR_PAD_LEFT);

            if ($statusNormalized === 'Resolved') {
                $notifTitle = 'Maintenance Request Resolved (' . $codeStr . ')';
                $notifMsg = 'Your maintenance request "' . $maintenance->title . '" has been resolved.' . ($remarks ? ' Remarks: ' . $remarks : '');
                $notifType = 'success';
            } elseif ($statusNormalized === 'Rejected') {
                $notifTitle = 'Maintenance Request Rejected (' . $codeStr . ')';
                $notifMsg = 'Your maintenance request "' . $maintenance->title . '" was rejected.' . ($remarks ? ' Reason: ' . $remarks : '');
                $notifType = 'danger';
            } elseif ($statusNormalized === 'In Progress') {
                $notifTitle = 'Maintenance In Progress (' . $codeStr . ')';
                $notifMsg = 'Your maintenance request "' . $maintenance->title . '" is now in progress.' . ($maintenance->assigned_to ? ' Assigned technician: ' . $maintenance->assigned_to . '.' : '') . ($remarks ? ' Note: ' . $remarks : '');
                $notifType = 'info';
            } else {
                $notifTitle = 'Maintenance Request Update (' . $codeStr . ')';
                $notifMsg = 'Your maintenance request "' . $maintenance->title . '" is pending review.' . ($remarks ? ' Note: ' . $remarks : '');
                $notifType = 'warning';
            }

            AppNotification::create([
                'user_id' => $maintenance->tenant->user_id,
                'title' => $notifTitle,
                'message' => $notifMsg,
                'type' => $notifType,
                'link' => route('tenant.maintenance.show', $maintenance->id),
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.maintenance.show', $maintenance->id)
            ->with('success', 'Maintenance request status updated to "' . $maintenance->status . '" successfully!');
    }

    public function destroy($id)
    {
        $maintenance = ($id instanceof MaintenanceRequest && $id->exists) ? $id : MaintenanceRequest::findOrFail($id);

        if ($maintenance->attachment_path) {
            FileUploadService::delete($maintenance->attachment_path);
        }

        $code = $maintenance->request_code;
        $maintenance->delete();

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Maintenance request ' . $code . ' deleted.');
    }
}
