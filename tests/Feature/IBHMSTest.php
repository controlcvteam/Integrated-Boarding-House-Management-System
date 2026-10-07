<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomRequest;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IBHMSTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Integrated Boarding House');
    }

    public function test_authenticated_root_redirects(): void
    {
        $admin = User::where('role', 'admin')->first();
        $responseAdmin = $this->actingAs($admin)->get('/');
        $responseAdmin->assertRedirect(route('admin.dashboard'));

        $approvedTenant = User::where('role', 'tenant')->where('account_status', 'approved')->first();
        $responseTenant = $this->actingAs($approvedTenant)->get('/');
        $responseTenant->assertRedirect(route('tenant.dashboard'));

        $pendingTenant = User::where('role', 'tenant')->where('account_status', 'pending')->first();
        if ($pendingTenant) {
            $responsePending = $this->actingAs($pendingTenant)->get('/');
            $responsePending->assertRedirect(route('auth.pending'));
        }

        $rejectedTenant = User::where('role', 'tenant')->where('account_status', 'rejected')->first();
        if ($rejectedTenant) {
            $responseRejected = $this->actingAs($rejectedTenant)->get('/');
            $responseRejected->assertRedirect(route('auth.rejected'));
        }
    }

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Tenant Registration');
    }

    public function test_guest_can_view_public_rooms(): void
    {
        $response = $this->get('/rooms');
        $response->assertStatus(200);
        $response->assertSee('Boarding House Rooms');
    }

    public function test_tenant_self_registration_creates_pending_account(): void
    {
        $email = 'newtenant_' . uniqid() . '@example.com';
        $response = $this->post('/register', [
            'name' => 'Test Applicant',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'contact_number' => '0917-999-0000',
            'address' => '123 Sampaloc St, Manila',
            'gender' => 'Female',
            'role' => 'admin', // Malicious attempt to register as admin!
        ]);

        $response->assertRedirect(route('account.pending'));

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('tenant', $user->role); // MUST remain tenant!
        $this->assertEquals('pending', $user->account_status); // MUST be pending!

        // Clean up test applicant to prevent leaving dummy pending accounts in dev/test DB
        $user->tenant?->roomRequests()->delete();
        $user->tenant?->delete();
        $user->delete();
    }

    public function test_pending_tenant_is_blocked_from_tenant_dashboard(): void
    {
        $createdTemp = false;
        $pendingUser = User::where('role', 'tenant')->where('account_status', 'pending')->first();
        if (!$pendingUser) {
            $pendingUser = User::create([
                'name' => 'Pending Bob',
                'email' => 'bob_' . uniqid() . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'account_status' => 'pending',
            ]);
            $createdTemp = true;
        }

        $response = $this->actingAs($pendingUser)->get('/tenant/dashboard');
        $response->assertRedirect(route('account.pending'));

        if ($createdTemp) {
            $pendingUser->tenant?->delete();
            $pendingUser->delete();
        }
    }

    public function test_rejected_tenant_is_blocked_from_tenant_dashboard(): void
    {
        $createdTemp = false;
        $rejectedUser = User::where('role', 'tenant')->where('account_status', 'rejected')->first();
        if (!$rejectedUser) {
            $rejectedUser = User::create([
                'name' => 'Rejected Alice',
                'email' => 'alice_' . uniqid() . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'account_status' => 'rejected',
                'rejection_reason' => 'No rooms available.',
            ]);
            $createdTemp = true;
        }

        $response = $this->actingAs($rejectedUser)->get('/tenant/dashboard');
        $response->assertRedirect(route('account.rejected'));

        if ($createdTemp) {
            $rejectedUser->tenant?->delete();
            $rejectedUser->delete();
        }
    }

    public function test_tenant_cannot_access_admin_dashboard(): void
    {
        $approvedUser = User::where('role', 'tenant')->where('account_status', 'approved')->first();
        $this->assertNotNull($approvedUser);

        $response = $this->actingAs($approvedUser)->get('/admin/dashboard');
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Rent Due Calendar');
        $response->assertSee('Overview of boarding house operations');
    }

    public function test_approved_tenant_can_access_tenant_dashboard(): void
    {
        $approvedUser = User::where('role', 'tenant')->where('account_status', 'approved')->whereHas('tenant')->first();
        $this->assertNotNull($approvedUser);

        $response = $this->actingAs($approvedUser)->get('/tenant/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Tenant Dashboard');
    }

    public function test_tenant_rooms_browsing_preserves_occupant_privacy(): void
    {
        $approvedUser = User::where('role', 'tenant')->where('account_status', 'approved')->first();
        $response = $this->actingAs($approvedUser)->get('/tenant/rooms');
        $response->assertStatus(200);

        // Occupant names like "Pedro Dela Cruz" or private notes should NOT be revealed
        $response->assertDontSee('Pedro Dela Cruz (Father)');
    }

    public function test_admin_can_record_cash_payment(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::with('room')->where('status', 'active')->first();
        $this->assertNotNull($tenant);

        // Clean any existing record for this test cycle
        Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', 11)
            ->where('billing_year', 2026)
            ->delete();

        $response = $this->actingAs($admin)->post('/admin/payments', [
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'amount' => $tenant->room->monthly_rent,
            'billing_month' => 11,
            'billing_year' => 2026,
            'payment_method' => 'cash',
            'payment_date' => '2026-11-01',
            'notes' => 'Advance cash payment for November.',
        ]);

        $response->assertRedirect();

        $payment = Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', 11)
            ->where('billing_year', 2026)
            ->where('payment_method', 'cash')
            ->first();

        $this->assertNotNull($payment);
        $this->assertEquals('verified', $payment->status);
    }

    public function test_admin_can_verify_pending_gcash_payment(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::with('room')->where('status', 'active')->first();

        // Ensure a pending payment exists for testing
        $pendingPayment = Payment::where('payment_method', 'gcash')->where('status', 'pending')->first();
        if (!$pendingPayment) {
            $pendingPayment = Payment::create([
                'tenant_id' => $tenant->id,
                'room_id' => $tenant->room_id,
                'amount' => $tenant->room->monthly_rent,
                'billing_month' => 12,
                'billing_year' => 2026,
                'payment_method' => 'gcash',
                'payment_date' => '2026-12-01',
                'gcash_reference' => '1009988776655',
                'status' => 'pending',
            ]);
        }

        $response = $this->actingAs($admin)->post("/admin/payments/{$pendingPayment->id}/verify");
        $response->assertRedirect();

        $pendingPayment->refresh();
        $this->assertEquals('verified', $pendingPayment->status);
    }

    public function test_tenant_can_submit_maintenance_request(): void
    {
        $tenantUser = User::where('role', 'tenant')
            ->where('account_status', 'approved')
            ->whereHas('tenant', fn($q) => $q->whereNotNull('room_id'))
            ->first();
        $this->assertNotNull($tenantUser);

        $response = $this->actingAs($tenantUser)->post('/tenant/maintenance', [
            'category' => 'Plumbing',
            'priority' => 'medium',
            'title' => 'Faucet handle loose',
            'description' => 'The hot/cold knob is spinning freely and needs a screw tightened.',
        ]);

        $response->assertRedirect(route('tenant.maintenance.index'));

        $this->assertDatabaseHas('maintenance_requests', [
            'tenant_id' => $tenantUser->tenant->id,
            'title' => 'Faucet handle loose',
            'category' => 'Plumbing',
        ]);
    }

    public function test_admin_reports_page_loads_all_tabs(): void
    {
        $admin = User::where('role', 'admin')->first();

        foreach (['collections', 'outstanding', 'occupancy', 'tenants', 'maintenance'] as $type) {
            $response = $this->actingAs($admin)->get("/admin/reports?type={$type}");
            $response->assertStatus(200);
        }
    }

    public function test_pending_tenant_page_renders_cleanly(): void
    {
        $pendingUser = User::where('role', 'tenant')->where('account_status', 'pending')->first();
        if (!$pendingUser) {
            $pendingUser = User::create([
                'name' => 'Pending Tester',
                'email' => 'pending_' . uniqid() . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'account_status' => 'pending',
            ]);
        }

        $response = $this->actingAs($pendingUser)->get('/account/pending');
        $response->assertStatus(200);
        $response->assertSee('Account Pending Approval');
        $response->assertSee(route('public.rooms'));
    }

    public function test_public_rooms_routes_accessible(): void
    {
        $response1 = $this->get(route('rooms.public'));
        $response1->assertStatus(200);

        $response2 = $this->get(route('public.rooms'));
        $response2->assertStatus(200);
    }

    public function test_admin_can_view_room_requests_without_missing_parameters(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/room-requests');
        $response->assertStatus(200);
        $response->assertSee('Room Requests');
    }

    public function test_admin_can_view_tenant_details(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::first();
        $response = $this->actingAs($admin)->get("/admin/tenants/{$tenant->id}");
        $response->assertStatus(200);
        $response->assertSee($tenant->full_name);
    }

    public function test_admin_can_view_pending_tenants(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/pending-tenants');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_maintenance_show_and_edit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $maintenance = \App\Models\MaintenanceRequest::first();
        if (!$maintenance) {
            $t = Tenant::first();
            $r = Room::first();
            $maintenance = \App\Models\MaintenanceRequest::create([
                'tenant_id' => $t ? $t->id : null,
                'room_id' => $r ? $r->id : null,
                'title' => 'Test Leaking Pipe',
                'category' => 'Plumbing',
                'priority' => 'High',
                'status' => 'Pending',
                'description' => 'Bathroom water leak',
            ]);
        }
        $this->assertNotNull($maintenance);

        $responseShow = $this->actingAs($admin)->get("/admin/maintenance/{$maintenance->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSee($maintenance->title);

        $responseEdit = $this->actingAs($admin)->get("/admin/maintenance/{$maintenance->id}/edit");
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee($maintenance->title);
    }

    public function test_admin_can_view_payment_show_and_edit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $payment = \App\Models\Payment::first();
        $this->assertNotNull($payment);

        $responseShow = $this->actingAs($admin)->get("/admin/payments/{$payment->id}");
        $responseShow->assertStatus(200);

        $responseEdit = $this->actingAs($admin)->get("/admin/payments/{$payment->id}/edit");
        $responseEdit->assertStatus(200);
    }

    public function test_admin_can_view_room_show_and_edit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $room = \App\Models\Room::first();
        $this->assertNotNull($room);

        $responseShow = $this->actingAs($admin)->get("/admin/rooms/{$room->id}");
        $responseShow->assertStatus(200);

        $responseEdit = $this->actingAs($admin)->get("/admin/rooms/{$room->id}/edit");
        $responseEdit->assertStatus(200);
    }

    public function test_admin_cannot_create_maintenance_request_and_only_updates_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // Accessing create should redirect with informational message
        $responseCreate = $this->actingAs($admin)->get(route('admin.maintenance.create'));
        $responseCreate->assertRedirect(route('admin.maintenance.index'));
        $responseCreate->assertSessionHas('info');

        // Posting new request as admin should also redirect
        $responseStore = $this->actingAs($admin)->post(route('admin.maintenance.store'), [
            'title' => 'Admin test request',
            'category' => 'Electrical',
            'priority' => 'high',
        ]);
        $responseStore->assertRedirect(route('admin.maintenance.index'));

        // Admin updating existing request updates status, remarks, staff
        $maintenance = \App\Models\MaintenanceRequest::first();
        if (!$maintenance) {
            $t = Tenant::first();
            $r = Room::first();
            $maintenance = \App\Models\MaintenanceRequest::create([
                'tenant_id' => $t ? $t->id : null,
                'room_id' => $r ? $r->id : null,
                'title' => 'Test Leaking Pipe',
                'category' => 'Plumbing',
                'priority' => 'High',
                'status' => 'Pending',
                'description' => 'Bathroom water leak',
            ]);
        }
        $this->assertNotNull($maintenance);
        $originalTitle = $maintenance->title;

        $responseUpdate = $this->actingAs($admin)->put(route('admin.maintenance.update', $maintenance->id), [
            'title' => 'Attempted Title Overwrite by Admin',
            'status' => 'In Progress',
            'assigned_to' => 'Kuya Bert (Electrician)',
            'remarks' => 'Scheduled inspection today.',
            'target_date' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $responseUpdate->assertRedirect(route('admin.maintenance.show', $maintenance->id));
        $maintenance->refresh();
        $this->assertEquals('In Progress', $maintenance->status);
        $this->assertEquals('Kuya Bert (Electrician)', $maintenance->assigned_to);
        // Tenant's original title is preserved!
        $this->assertEquals($originalTitle, $maintenance->title);
    }

    public function test_record_payment_requires_payment_method_with_exact_validation_message(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::with('room')->where('status', 'active')->first();
        $this->assertNotNull($tenant);

        // Submit without payment_method
        $response = $this->actingAs($admin)->post(route('admin.payments.record-cash-quick'), [
            'tenant_id' => $tenant->id,
            'billing_month' => 10,
            'billing_year' => 2026,
            'amount' => 3500,
            'payment_date' => '2026-10-04',
            // 'payment_method' missing on purpose
        ]);

        $response->assertSessionHasErrors([
            'payment_method' => 'The payment method field is required.'
        ]);
    }

    public function test_ui_views_are_landlady_free(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenantUser = User::where('role', 'tenant')->where('account_status', 'approved')->whereHas('tenant')->first();

        // Admin Dashboard
        $adminDash = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminDash->assertStatus(200);
        $adminDash->assertDontSee('Landlady', false);

        // Admin Maintenance Index
        $adminMaint = $this->actingAs($admin)->get(route('admin.maintenance.index'));
        $adminMaint->assertStatus(200);
        $adminMaint->assertDontSee('Landlady', false);

        // Tenant Dashboard
        $tenantDash = $this->actingAs($tenantUser)->get(route('tenant.dashboard'));
        $tenantDash->assertStatus(200);
        $tenantDash->assertDontSee('Landlady', false);

        // Tenant Maintenance
        $tenantMaint = $this->actingAs($tenantUser)->get(route('tenant.maintenance.index'));
        $tenantMaint->assertStatus(200);
        $tenantMaint->assertDontSee('Landlady', false);
    }

    public function test_admin_settings_clean_and_updates_gcash_info(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();

        // 1. Admin Settings page does NOT show System Information or Appearance & UI Preferences
        $response = $this->actingAs($admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertDontSee('System Information');
        $response->assertDontSee('Appearance & UI Preferences');
        $response->assertSee('GCash Payment & QR Code Settings', false);

        // 2. Admin updates GCash settings
        $fakeQr = UploadedFile::fake()->create('custom-gcash-qr.svg', 10, 'image/svg+xml');
        $updateResp = $this->actingAs($admin)->put(route('admin.settings.gcash'), [
            'gcash_name' => 'Official Landlord GCash',
            'gcash_number' => '0999-123-4567',
            'gcash_qr_code' => $fakeQr,
        ]);
        $updateResp->assertRedirect(route('admin.settings.index'));
        $updateResp->assertSessionHas('success');

        $this->assertEquals('Official Landlord GCash', Setting::get('gcash_name'));
        $this->assertEquals('0999-123-4567', Setting::get('gcash_number'));
        $savedPath = Setting::get('gcash_qr_path');
        $this->assertNotNull($savedPath);
        Storage::disk('public')->assertExists($savedPath);
    }

    public function test_tenant_settings_removes_display_preferences(): void
    {
        $tenantUser = User::where('role', 'tenant')->where('account_status', 'approved')->first();
        $response = $this->actingAs($tenantUser)->get(route('tenant.settings.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Display Preferences');
    }

    public function test_tenant_gcash_payment_page_displays_configured_qr_and_info(): void
    {
        Setting::set('gcash_name', 'Property Management Office');
        Setting::set('gcash_number', '0918-987-6543');

        $tenantUser = User::where('role', 'tenant')->where('account_status', 'approved')->whereHas('tenant.room')->first();
        if (!$tenantUser) {
            $tenantUser = User::where('role', 'tenant')->where('account_status', 'approved')->first();
            if ($tenantUser && $tenantUser->tenant) {
                $room = Room::first();
                $tenantUser->tenant->update(['room_id' => $room->id]);
            }
        }
        $this->assertNotNull($tenantUser);

        $response = $this->actingAs($tenantUser)->get(route('tenant.payments.submit'));
        $response->assertStatus(200);
        $response->assertSee('Property Management Office');
        $response->assertSee('0918-987-6543');
        $response->assertSee('Scan GCash QR Code');
    }

    public function test_room_request_approval_updates_tenant_and_vacates_old_room(): void
    {
        $admin = User::where('role', 'admin')->first();

        $oldRoomNum = 'TR-' . rand(10000, 99999);
        $newRoomNum = 'TR-' . rand(10000, 99999);

        // Create Old Room
        $oldRoom = Room::create([
            'room_number' => $oldRoomNum,
            'room_type' => 'Single',
            'capacity' => 1,
            'monthly_rent' => 3000,
            'floor' => 2,
            'manual_available' => false, // Initially marked unavailable
        ]);

        // Create New Target Room
        $newRoom = Room::create([
            'room_number' => $newRoomNum,
            'room_type' => 'Single',
            'capacity' => 1,
            'monthly_rent' => 3500,
            'floor' => 2,
            'manual_available' => true,
        ]);

        // Create Tenant assigned to Old Room
        $user = User::create([
            'name' => 'Transferring Tenant',
            'email' => 'transfer.' . uniqid() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);

        $tenant = Tenant::create([
            'user_id' => $user->id,
            'room_id' => $oldRoom->id,
            'tenant_code' => 'TEN-TEST-' . rand(100, 999),
            'full_name' => 'Transferring Tenant',
            'contact_number' => '09123456789',
            'gender' => 'Male',
            'date_of_birth' => '2000-01-01',
            'move_in_date' => now()->toDateString(),
            'status' => 'active',
            'address' => 'Test Address',
        ]);

        // Create Room Request for new room
        $roomRequest = RoomRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'room_id' => $newRoom->id,
            'status' => 'pending',
            'preferred_move_in_date' => now()->addDays(5)->toDateString(),
        ]);

        // Admin approves room request
        $response = $this->actingAs($admin)->post(route('admin.room-requests.approve', $roomRequest->id), [
            'admin_remarks' => 'Approved transfer to Room ' . $newRoomNum,
        ]);
        $response->assertSessionHas('success');

        // Verify tenant is now assigned to new room
        $tenant->refresh();
        $this->assertEquals($newRoom->id, $tenant->room_id);

        // Verify old room is now empty and marked manual_available = true
        $oldRoom->refresh();
        $this->assertEquals(0, $oldRoom->activeTenants()->count());
        $this->assertTrue($oldRoom->manual_available);

        // Clean up
        $roomRequest->delete();
        $tenant->delete();
        $user->delete();
        $oldRoom->delete();
        $newRoom->delete();
    }

    public function test_maintenance_request_status_update_resolved_or_rejected_syncs_and_notifies(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::with('user', 'room')->where('status', 'active')->first();
        $this->assertNotNull($tenant);

        // Create maintenance request
        $maint = MaintenanceRequest::create([
            'request_code' => 'MNT-TEST-' . rand(1000, 9999),
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'title' => 'Leaking Faucet in Bathroom',
            'description' => 'Water is constantly dripping from the faucet.',
            'category' => 'Plumbing',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]);

        // 1. Admin marks as Resolved
        $respResolved = $this->actingAs($admin)->put(route('admin.maintenance.update', $maint->id), [
            'status' => 'Resolved',
            'remarks' => 'Replaced faucet washer and valve.',
        ]);
        $respResolved->assertRedirect(route('admin.maintenance.show', $maint->id));

        $maint->refresh();
        $this->assertEquals('Resolved', $maint->status);
        $this->assertNotNull($maint->resolved_at);
        $this->assertEquals('Replaced faucet washer and valve.', $maint->remarks);
        $this->assertEquals('Replaced faucet washer and valve.', $maint->admin_remarks);

        // Tenant views ticket show page
        $tenantView = $this->actingAs($tenant->user)->get(route('tenant.maintenance.show', $maint->id));
        $tenantView->assertStatus(200);
        $tenantView->assertSee('Resolved');
        $tenantView->assertSee('Replaced faucet washer and valve.');

        // Notification created for tenant
        $notifResolved = AppNotification::where('user_id', $tenant->user_id)
            ->where('title', 'like', '%Resolved%')
            ->first();
        $this->assertNotNull($notifResolved);

        // 2. Admin marks as Rejected
        $respRejected = $this->actingAs($admin)->put(route('admin.maintenance.update', $maint->id), [
            'status' => 'Rejected',
            'remarks' => 'Item is tenant-provided appliance not covered by boarding house.',
        ]);
        $respRejected->assertRedirect(route('admin.maintenance.show', $maint->id));

        $maint->refresh();
        $this->assertEquals('Rejected', $maint->status);
        $this->assertNull($maint->resolved_at);

        // Tenant views ticket show page
        $tenantViewRej = $this->actingAs($tenant->user)->get(route('tenant.maintenance.show', $maint->id));
        $tenantViewRej->assertStatus(200);
        $tenantViewRej->assertSee('Rejected');
        $tenantViewRej->assertSee('Item is tenant-provided appliance not covered');

        // Notification created for tenant
        $notifRejected = AppNotification::where('user_id', $tenant->user_id)
            ->where('title', 'like', '%Rejected%')
            ->first();
        $this->assertNotNull($notifRejected);
    }

    public function test_delete_and_approve_reject_buttons_styling_and_deletion_functionality(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Verify Approve/Reject styling on Pending Tenants Show
        $pendingTenant = Tenant::whereHas('user', function($q) {
            $q->where('account_status', 'pending');
        })->first();

        if (!$pendingTenant) {
            // Create a pending applicant for test
            $user = User::create([
                'name' => 'Pending Applicant Test',
                'email' => 'pending_' . uniqid() . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'account_status' => 'pending',
            ]);
            $pendingTenant = Tenant::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'full_name' => 'Pending Applicant Test',
                'contact_number' => '09123456789',
                'address' => 'Test Address',
            ]);
        }

        $pendingView = $this->actingAs($admin)->get(route('admin.pending-tenants.show', $pendingTenant->id));
        $pendingView->assertStatus(200);
        $pendingView->assertSee('btn-success-custom', false);
        $pendingView->assertSee('btn-outline-danger-custom', false);

        // 2. Verify Room Requests view has vibrant green Approve and red Reject buttons
        $pendingReq = RoomRequest::where('status', 'pending')->first();
        if (!$pendingReq) {
            $u = User::create([
                'name' => 'Room Req Applicant',
                'email' => 'roomreq_' . uniqid() . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'account_status' => 'approved',
            ]);
            $r = Room::first();
            RoomRequest::create([
                'user_id' => $u->id,
                'room_id' => $r->id,
                'status' => 'pending',
            ]);
        }

        $roomReqView = $this->actingAs($admin)->get(route('admin.room-requests.index'));
        $roomReqView->assertStatus(200);
        $roomReqView->assertSee('btn-success-custom', false);
        $roomReqView->assertSee('btn-outline-danger-custom', false);

        // 3. Verify Payments Show for pending GCash payment
        $pendingPayment = Payment::where('status', 'pending')->where('payment_method', 'gcash')->first();
        if ($pendingPayment) {
            $payShow = $this->actingAs($admin)->get(route('admin.payments.show', $pendingPayment->id));
            $payShow->assertStatus(200);
            $payShow->assertSee('btn-success-custom', false);
            $payShow->assertSee('btn-outline-danger-custom', false);
        }

        // 4. Verify Delete action-btn classes on all 4 index tables
        $inactiveTenant = Tenant::whereIn('status', ['inactive', 'moved_out'])->first();
        if (!$inactiveTenant) {
            $userTemp = User::create([
                'name' => 'Inactive Delete Check',
                'email' => 'inact_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'tenant',
                'account_status' => 'approved',
            ]);
            Tenant::create([
                'user_id' => $userTemp->id,
                'full_name' => 'Inactive Delete Check',
                'tenant_code' => 'TEN-DEL-' . uniqid(),
                'status' => 'inactive',
            ]);
        }
        $tenantsIndex = $this->actingAs($admin)->get(route('admin.tenants.index'));
        $tenantsIndex->assertStatus(200);
        $tenantsIndex->assertSee('action-btn delete-btn', false);

        $roomsIndex = $this->actingAs($admin)->get(route('admin.rooms.index'));
        $roomsIndex->assertStatus(200);
        $roomsIndex->assertSee('action-btn delete-btn', false);

        $paymentsIndex = $this->actingAs($admin)->get(route('admin.payments.index'));
        $paymentsIndex->assertStatus(200);
        $paymentsIndex->assertSee('action-btn', false);

        // 5. Test Deletion Operations for each resource
        // 5a. Delete Maintenance Request
        $testRoom = Room::first();
        $testMaint = MaintenanceRequest::create([
            'tenant_id' => $pendingTenant->id,
            'room_id' => $testRoom->id,
            'title' => 'To Be Deleted Maintenance',
            'category' => 'Plumbing',
            'priority' => 'Low',
            'status' => 'Pending',
            'description' => 'Test maintenance request to delete',
        ]);

        $maintIndex = $this->actingAs($admin)->get(route('admin.maintenance.index'));
        $maintIndex->assertStatus(200);
        $maintIndex->assertSee('action-btn delete-btn', false);
        $delMaintResp = $this->actingAs($admin)->delete(route('admin.maintenance.destroy', $testMaint->id));
        $delMaintResp->assertRedirect(route('admin.maintenance.index'));
        $this->assertDatabaseMissing('maintenance_requests', ['id' => $testMaint->id]);

        // 5b. Delete Payment
        $testPayment = Payment::create([
            'tenant_id' => $pendingTenant->id,
            'room_id' => $testRoom->id,
            'amount' => 1234,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'billing_month' => 10,
            'billing_year' => 2026,
            'status' => 'paid',
        ]);
        $delPayResp = $this->actingAs($admin)->delete(route('admin.payments.destroy', $testPayment->id));
        $delPayResp->assertRedirect(route('admin.payments.index'));
        $delPayResp->assertSessionHas('error');
        $this->assertDatabaseHas('payments', ['id' => $testPayment->id]);

        // 5c. Delete Room (unoccupied)
        $testRoom = Room::create([
            'room_number' => 'DEL-999',
            'room_type' => 'Single Bed',
            'capacity' => 1,
            'monthly_rent' => 2500,
            'manual_available' => true,
        ]);
        $delRoomResp = $this->actingAs($admin)->delete(route('admin.rooms.destroy', $testRoom->id));
        $delRoomResp->assertRedirect(route('admin.rooms.index'));
        $this->assertDatabaseMissing('rooms', ['id' => $testRoom->id]);

        // 5d. Delete Tenant
        $testUser = User::create([
            'name' => 'To Be Deleted Tenant',
            'email' => 'deltenant_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);
        $testTenant = Tenant::create([
            'user_id' => $testUser->id,
            'full_name' => 'To Be Deleted Tenant',
            'contact_number' => '09999999999',
            'address' => 'Test Address',
            'status' => 'moved_out',
        ]);
        $delTenantResp = $this->actingAs($admin)->delete(route('admin.tenants.destroy', $testTenant->id));
        $delTenantResp->assertRedirect(route('admin.tenants.index'));
        $this->assertDatabaseMissing('tenants', ['id' => $testTenant->id]);
    }

    public function test_admin_can_edit_limited_tenant_info_and_tenant_can_update_own_profile(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenantUser = User::where('role', 'tenant')->where('account_status', 'approved')->whereHas('tenant')->first();
        $this->assertNotNull($admin);
        $this->assertNotNull($tenantUser);
        $tenant = $tenantUser->tenant;
        $this->assertNotNull($tenant);

        // 1. Admin GET edit route is accessible (200)
        $editGetResp = $this->actingAs($admin)->get(route('admin.tenants.edit', $tenant->id));
        $editGetResp->assertStatus(200);
        $editGetResp->assertSee('Edit Tenant Information');

        // 2. Admin PUT update route updates allowed tenant fields
        $editPutResp = $this->actingAs($admin)->put(route('admin.tenants.update', $tenant->id), [
            'full_name' => 'Admin Updated Name',
            'contact_number' => '09991234567',
            'move_in_date' => '2026-01-01',
            'status' => 'active',
        ]);
        $editPutResp->assertRedirect(route('admin.tenants.show', $tenant->id));
        $editPutResp->assertSessionHas('success');

        // 3. UI checks: Edit button is present in admin tenants index and show views
        $indexResp = $this->actingAs($admin)->get(route('admin.tenants.index'));
        $indexResp->assertSee(route('admin.tenants.edit', $tenant->id));

        $showResp = $this->actingAs($admin)->get(route('admin.tenants.show', $tenant->id));
        $showResp->assertSee(route('admin.tenants.edit', $tenant->id));

        // 4. Tenant CAN update their own profile information
        $originalName = $tenantUser->name;
        $originalPhone = $tenantUser->contact_number;
        $originalAddress = $tenantUser->address;

        $updateResp = $this->actingAs($tenantUser)->put(route('tenant.settings.profile'), [
            'name' => 'Updated Tenant Self',
            'contact_number' => '0919-888-7777',
            'address' => 'Updated Unit 401, Sampaloc, Manila',
            'gender' => 'Female',
            'date_of_birth' => '1998-05-15',
            'emergency_contact_name' => 'Emergency Guardian',
            'emergency_contact_number' => '0918-111-2222',
        ]);

        $updateResp->assertRedirect(route('tenant.settings.index'));
        $updateResp->assertSessionHas('success');

        $tenantUser->refresh();
        $tenant->refresh();

        $this->assertEquals('Updated Tenant Self', $tenantUser->name);
        $this->assertEquals('Updated Tenant Self', $tenant->full_name);
        $this->assertEquals('0919-888-7777', $tenantUser->contact_number);
        $this->assertEquals('0919-888-7777', $tenant->contact_number);
        $this->assertEquals('Female', $tenant->gender);
        $this->assertEquals('Emergency Guardian', $tenant->emergency_contact_name);

        // Restore original details so test doesn't mutate test fixtures
        $tenantUser->update([
            'name' => $originalName,
            'contact_number' => $originalPhone,
            'address' => $originalAddress,
        ]);
        $tenant->update([
            'full_name' => $originalName,
            'contact_number' => $originalPhone,
            'address' => $originalAddress,
        ]);
    }

    public function test_admin_can_delete_room_request(): void
    {
        $admin = User::where('role', 'admin')->first();
        $room = Room::first();
        $tenant = Tenant::first();

        $req = RoomRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $tenant->user_id,
            'room_id' => $room->id,
            'status' => 'pending',
            'notes' => 'Test room request to be deleted',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/room-requests/{$req->id}");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNull(RoomRequest::find($req->id));
    }

    public function test_partial_and_paid_payments_displayed_and_calculated(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::with('room')->first();

        // Record a partial payment (less than monthly rent)
        $partialPayment = Payment::create([
            'payment_code' => 'TEST-PARTIAL-' . uniqid(),
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'billing_month' => 3,
            'billing_year' => 2026,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => '2027-01-01',
            'status' => 'partial',
            'remarks' => 'Partial deposit',
        ]);

        // Check admin payments index shows Partial badge
        $adminIndexResp = $this->actingAs($admin)->get('/admin/payments');
        $adminIndexResp->assertOk();
        $adminIndexResp->assertSee('Partial');

        // Check tenant payments index shows Partial badge
        $tenantResp = $this->actingAs($tenant->user)->get('/tenant/payments');
        $tenantResp->assertOk();
        $tenantResp->assertSee('Partial');

        $partialPayment->delete();
    }

    public function test_overdue_rent_shows_red_alert_to_tenant(): void
    {
        $tenant = Tenant::with(['room', 'user'])->first();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Unpaid state: should see "PLEASE PAY YOUR MONTHLY RENT"
        // Clean any existing payment for current month during this test
        $existingPayments = Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $currentMonth)
            ->where('billing_year', $currentYear)
            ->get();
        Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $currentMonth)
            ->where('billing_year', $currentYear)
            ->delete();

        $response = $this->actingAs($tenant->user)->get('/tenant/dashboard');
        $response->assertOk();
        $response->assertSee('PLEASE PAY YOUR MONTHLY RENT');
        $response->assertSee('Integrated Boarding House');
        $response->assertSee('favicon.svg');

        // 2. Partial state: should see "PLEASE PAY THE FULL AMOUNT"
        $partial = Payment::create([
            'payment_code' => 'TEST-ALERT-PARTIAL-' . uniqid(),
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'billing_month' => $currentMonth,
            'billing_year' => $currentYear,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'status' => 'partial',
        ]);

        $responsePartial = $this->actingAs($tenant->user)->get('/tenant/dashboard');
        $responsePartial->assertOk();
        $responsePartial->assertSee('PLEASE PAY THE FULL AMOUNT');

        // 3. Paid in full state: "PLEASE PAY" alert banner is removed completely on both dashboard & payment history
        $partial->update([
            'status' => 'verified',
            'amount' => $tenant->room ? $tenant->room->monthly_rent : 5000,
        ]);

        $responsePaid = $this->actingAs($tenant->user)->get('/tenant/dashboard');
        $responsePaid->assertOk();
        $responsePaid->assertDontSee('PLEASE PAY YOUR MONTHLY RENT');
        $responsePaid->assertDontSee('PLEASE PAY THE FULL AMOUNT');

        $responsePaidPayments = $this->actingAs($tenant->user)->get('/tenant/payments');
        $responsePaidPayments->assertOk();
        $responsePaidPayments->assertDontSee('PLEASE PAY YOUR MONTHLY RENT');
        $responsePaidPayments->assertDontSee('PLEASE PAY THE FULL AMOUNT');

        // Cleanup
        $partial->delete();
        foreach ($existingPayments as $ep) {
            Payment::create($ep->getAttributes());
        }
    }

    public function test_public_rooms_and_register_views(): void
    {
        $room = Room::first();

        // Public rooms page has wide container and apply link
        $publicResp = $this->get('/rooms');
        $publicResp->assertOk();
        $publicResp->assertSee('Available Boarding House Rooms');
        $publicResp->assertSee('favicon.svg');

        // Register form accepts room_id query parameter and arranges rooms
        $registerResp = $this->get("/register?room_id={$room->id}");
        $registerResp->assertOk();
        $registerResp->assertSee('Browse & Select Preferred Room', false);
        $registerResp->assertSee('Room ' . $room->room_number);
    }

    public function test_available_rooms_search_finds_related_rooms_by_keyword_synonym_and_availability(): void
    {
        // 1. Search "available" finds available rooms
        $respAvail = $this->get('/rooms?search=available');
        $respAvail->assertOk();
        $respAvail->assertSee('Room 002');

        // 2. Search "room 2" or "002" finds Room 002
        $respRoom = $this->get('/rooms?search=room+2');
        $respRoom->assertOk();
        $respRoom->assertSee('Room 002');

        // 3. Search "wifi" finds Room 002 (which has Wi-Fi Access amenity)
        $respWifi = $this->get('/rooms?search=wifi');
        $respWifi->assertOk();
        $respWifi->assertSee('Room 002');

        // 4. Search "aircon" finds Room 002 (which has Air Conditioner amenity)
        $respAircon = $this->get('/rooms?search=aircon');
        $respAircon->assertOk();
        $respAircon->assertSee('Room 002');

        // 5. Tenant rooms search also works with synonyms and related keywords
        $tenant = Tenant::first();
        $respTenant = $this->actingAs($tenant->user)->get('/tenant/rooms?search=wifi');
        $respTenant->assertOk();
        $respTenant->assertSee('Room 002');
    }

    public function test_calendar_displays_amount_paid_including_partial_payments(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::where('status', 'active')->whereNotNull('room_id')->with(['room', 'user'])->first();
        if (!$tenant) {
            $tenant = Tenant::with(['room', 'user'])->first();
            $room = Room::first();
            $tenant->update([
                'status' => 'active',
                'room_id' => $room->id,
            ]);
        }
        $tenant->update([
            'move_in_date' => now()->startOfMonth(),
        ]);
        $tenant->load('room', 'user');
        $calMonth = now()->month;
        $calYear = now()->year;

        // Clean any existing payment for current month
        $existing = Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $calMonth)
            ->where('billing_year', $calYear)
            ->get();
        Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $calMonth)
            ->where('billing_year', $calYear)
            ->delete();

        // Create partial payment of 500
        $partial = Payment::create([
            'payment_code' => 'TEST-CAL-PARTIAL-' . uniqid(),
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'billing_month' => $calMonth,
            'billing_year' => $calYear,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'status' => 'partial',
        ]);

        $resp = $this->actingAs($admin)->get("/admin/dashboard?cal_month={$calMonth}&cal_year={$calYear}");
        $resp->assertOk();
        // Should display the partial amount paid right in the calendar event badge
        $resp->assertSee('Paid: ₱500.00');
        $resp->assertSee('Partially Paid');

        // Clean up
        $partial->delete();
        foreach ($existing as $ep) {
            Payment::create($ep->getAttributes());
        }
    }

    public function test_tenant_can_edit_and_delete_own_maintenance_request(): void
    {
        $tenant = Tenant::where('status', 'active')->has('user')->with('user', 'room')->first();
        if (!$tenant || !$tenant->room) {
            $this->markTestSkipped('No active tenant with room found.');
        }

        $user = $tenant->user;

        // Create a maintenance request
        $maint = \App\Models\MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'category' => 'Plumbing',
            'priority' => 'medium',
            'title' => 'Original Issue Title',
            'description' => 'Original Description of problem',
            'status' => 'pending',
        ]);

        // Tenant can access edit form
        $response = $this->actingAs($user)->get("/tenant/maintenance/{$maint->id}/edit");
        $response->assertOk();
        $response->assertSee('Original Issue Title');

        // Tenant can update it
        $updateResp = $this->actingAs($user)->put("/tenant/maintenance/{$maint->id}", [
            'category' => 'Electrical',
            'priority' => 'high',
            'title' => 'Updated Issue Title',
            'description' => 'Updated Description of problem',
        ]);
        $updateResp->assertRedirect('/tenant/maintenance');

        $maint->refresh();
        $this->assertEquals('Updated Issue Title', $maint->title);
        $this->assertEquals('Electrical', $maint->category);
        $this->assertEquals('high', $maint->priority);

        // Tenant can delete it
        $delResp = $this->actingAs($user)->delete("/tenant/maintenance/{$maint->id}");
        $delResp->assertRedirect('/tenant/maintenance');

        $this->assertDatabaseMissing('maintenance_requests', [
            'id' => $maint->id,
        ]);
    }

    public function test_tenant_can_submit_cash_payment_without_proof_as_pending(): void
    {
        $tenant = Tenant::where('status', 'active')->has('user')->with('user', 'room')->first();
        if (!$tenant || !$tenant->room) {
            $this->markTestSkipped('No active tenant with room found.');
        }

        $user = $tenant->user;

        $submitResp = $this->actingAs($user)->post('/tenant/payments/submit-gcash', [
            'payment_method' => 'cash',
            'billing_month' => now()->month,
            'billing_year' => now()->year,
            'amount' => 2500.00,
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Paid direct cash to landlord',
        ]);

        $submitResp->assertRedirect('/tenant/payments');

        $this->assertDatabaseHas('payments', [
            'tenant_id' => $tenant->id,
            'payment_method' => 'cash',
            'amount' => 2500.00,
            'status' => 'pending',
        ]);

        // Clean up
        Payment::where('tenant_id', $tenant->id)
            ->where('amount', 2500.00)
            ->where('notes', 'Paid direct cash to landlord')
            ->delete();
    }

    public function test_landlord_cannot_edit_payment_without_reason(): void
    {
        $admin = User::where('role', 'admin')->first();
        $payment = Payment::first();
        if (!$admin || !$payment) {
            $this->markTestSkipped('No admin or payment record found.');
        }

        $resp = $this->actingAs($admin)->put("/admin/payments/{$payment->id}", [
            'amount' => 3000,
            'billing_month' => $payment->billing_month,
            'billing_year' => $payment->billing_year,
            'payment_date' => '2026-10-01',
            'payment_method' => 'cash',
            'status' => 'paid',
            // Missing edit_reason
        ]);

        $resp->assertSessionHasErrors('edit_reason');
    }

    public function test_landlord_editing_payment_creates_audit_history_and_edited_flag(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = Tenant::where('status', 'active')->first();
        if (!$admin || !$tenant) {
            $this->markTestSkipped('No admin or tenant found.');
        }

        // Create test payment
        $payment = Payment::create([
            'payment_code' => 'TEST-AUDIT-' . uniqid(),
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'billing_month' => 9,
            'billing_year' => 2026,
            'amount' => 5000.00,
            'payment_method' => 'gcash',
            'payment_date' => '2026-09-05',
            'status' => 'paid',
            'is_edited' => false,
        ]);

        $this->assertFalse((bool)$payment->is_edited);

        // Edit payment with reason
        $resp = $this->actingAs($admin)->put("/admin/payments/{$payment->id}", [
            'amount' => 3500.00,
            'billing_month' => 10,
            'billing_year' => 2026,
            'payment_date' => '2026-10-05',
            'payment_method' => 'cash',
            'status' => 'paid',
            'edit_reason' => 'Incorrect amount entered by tenant',
        ]);

        $resp->assertRedirect("/admin/payments/{$payment->id}");

        $payment->refresh();
        $this->assertTrue((bool)$payment->is_edited);
        $this->assertEquals(3500.00, (float)$payment->amount);
        $this->assertEquals('cash', $payment->payment_method);
        $this->assertEquals(10, $payment->billing_month);

        // Check that PaymentEditHistory was created
        $this->assertDatabaseHas('payment_edit_histories', [
            'payment_id' => $payment->id,
            'reason' => 'Incorrect amount entered by tenant',
        ]);

        // Check json edit history endpoint
        $historyResp = $this->actingAs($admin)->get("/admin/payments/{$payment->id}/edit-history");
        $historyResp->assertOk();
        $historyResp->assertJsonPath('payment.is_edited', true);
        $historyResp->assertJsonPath('total_edits', 1);

        // Verify admin cannot delete payment
        $delResp = $this->actingAs($admin)->delete("/admin/payments/{$payment->id}");
        $delResp->assertRedirect('/admin/payments');
        // Payment is still in database!
        $this->assertDatabaseHas('payments', ['id' => $payment->id]);

        // Clean up test data
        \App\Models\PaymentEditHistory::where('payment_id', $payment->id)->delete();
        $payment->delete();
    }
}
