<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TenantPaymentAdminTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('role', 'admin')->first();
        if (!$this->admin) {
            $this->admin = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin_test_' . uniqid() . '@example.com',
                'role' => 'admin',
                'account_status' => 'approved',
            ]);
        }
    }

    public function test_admin_payment_create_displays_all_tenants(): void
    {
        $room = Room::first();

        $u1 = User::create([
            'name' => 'Active Tenant User',
            'email' => 'active_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);

        $activeTenant = Tenant::create([
            'user_id' => $u1->id,
            'full_name' => 'Active Tenant ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09123456789',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1995-05-15',
            'move_in_date' => now()->toDateString(),
            'status' => 'active',
            'address' => 'Test Address 1',
        ]);

        $u2 = User::create([
            'name' => 'Pending Tenant User',
            'email' => 'pending_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'pending',
        ]);

        $pendingTenant = Tenant::create([
            'user_id' => $u2->id,
            'full_name' => 'Pending Tenant ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09123456788',
            'room_id' => $room->id,
            'gender' => 'Female',
            'date_of_birth' => '1998-08-20',
            'move_in_date' => now()->toDateString(),
            'status' => 'pending',
            'address' => 'Test Address 2',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.create'));
        $response->assertStatus(200);

        // Verify both active and pending tenants appear in the select options
        $response->assertSee($activeTenant->full_name);
        $response->assertSee($pendingTenant->full_name);
        $response->assertSee('Active');
        $response->assertSee('Pending');
    }

    public function test_admin_can_edit_limited_tenant_information(): void
    {
        $tenantUser = User::create([
            'name' => 'Original Name',
            'email' => 'tenant_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);

        $room = Room::first();

        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'full_name' => 'Original Name',
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09111111111',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1996-01-01',
            'move_in_date' => '2026-01-01',
            'status' => 'active',
            'address' => 'Original Address',
            'nationality' => 'Filipino',
            'notes' => 'Original Notes',
        ]);

        // Access edit form - should only have the 5 limited fields
        $editResp = $this->actingAs($this->admin)->get(route('admin.tenants.edit', $tenant->id));
        $editResp->assertStatus(200);
        $editResp->assertSee('Original Name');
        $editResp->assertSee('Edit Tenant Information');
        $editResp->assertSee('Moved In Date');
        // Non-editable fields shouldn't be form inputs in this view
        $editResp->assertDontSee('name="date_of_birth"', false);
        $editResp->assertDontSee('name="nationality"', false);

        // Update tenant information with the 5 allowed fields
        $updateResp = $this->actingAs($this->admin)->put(route('admin.tenants.update', $tenant->id), [
            'full_name' => 'Updated Full Name',
            'contact_number' => '09222222222',
            'room_id' => $room->id,
            'move_in_date' => '2026-02-01',
            'status' => 'active',
        ]);

        $updateResp->assertRedirect(route('admin.tenants.show', $tenant->id));
        $updateResp->assertSessionHas('success');

        // Verify changes in database
        $tenant->refresh();
        $this->assertEquals('Updated Full Name', $tenant->full_name);
        $this->assertEquals('09222222222', $tenant->contact_number);
        $this->assertEquals('2026-02-01', $tenant->move_in_date->format('Y-m-d'));

        // Address and notes should remain untouched
        $this->assertEquals('Original Address', $tenant->address);
        $this->assertEquals('Original Notes', $tenant->notes);

        $tenantUser->refresh();
        $this->assertEquals('Updated Full Name', $tenantUser->name);
    }

    public function test_inactive_tenant_has_delete_action_and_can_be_deleted(): void
    {
        $tenantUser = User::create([
            'name' => 'Inactive Test Tenant',
            'email' => 'inactive_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);

        $room = Room::first();

        $inactiveTenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'full_name' => 'Inactive Test Tenant',
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09444444444',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1995-05-15',
            'move_in_date' => '2026-01-01',
            'status' => 'inactive',
            'address' => 'Test Address',
        ]);

        // Check tenants index renders delete button for inactive tenant
        $indexResp = $this->actingAs($this->admin)->get(route('admin.tenants.index', ['status' => 'inactive']));
        $indexResp->assertStatus(200);
        $indexResp->assertSee('Delete Inactive Tenant');

        // Check tenants show page has delete button
        $showResp = $this->actingAs($this->admin)->get(route('admin.tenants.show', $inactiveTenant->id));
        $showResp->assertStatus(200);
        $showResp->assertSee('Delete');

        // Execute delete
        $delResp = $this->actingAs($this->admin)->delete(route('admin.tenants.destroy', $inactiveTenant->id));
        $delResp->assertRedirect(route('admin.tenants.index'));
        $this->assertDatabaseMissing('tenants', ['id' => $inactiveTenant->id]);
    }

    public function test_admin_dashboard_does_not_have_record_payment_button(): void
    {
        // Admin dashboard should NOT contain "Record Payment"
        $dashResp = $this->actingAs($this->admin)->get('/admin/dashboard');
        $dashResp->assertStatus(200);
        $dashResp->assertDontSee('Record Payment');

        // But Admin Payments section SHOULD contain "Record Payment"
        $paymentsResp = $this->actingAs($this->admin)->get(route('admin.payments.index'));
        $paymentsResp->assertStatus(200);
        $paymentsResp->assertSee('Record Payment');
    }

    public function test_cash_payment_and_verified_gcash_payment_notifications(): void
    {
        $tenantUser = User::create([
            'name' => 'Notification Test Tenant',
            'email' => 'notif_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);

        $room = Room::first();

        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'full_name' => 'Notification Test Tenant',
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09333333333',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1995-05-15',
            'move_in_date' => '2026-01-01',
            'status' => 'active',
            'address' => 'Pay Address',
        ]);

        // Record Cash Payment
        $cashResp = $this->actingAs($this->admin)->post(route('admin.payments.store'), [
            'tenant_id' => $tenant->id,
            'billing_month' => 6,
            'billing_year' => 2026,
            'amount' => 2000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-10',
            'remarks' => 'Cash test',
        ]);

        $cashResp->assertSessionHas('success');
        $this->assertStringContainsString('Cash Payment Succesful', session('success'));

        // Verify Cash notification created with title "Cash Payment Succesful"
        $cashNotif = AppNotification::where('user_id', $tenantUser->id)
            ->where('title', 'Cash Payment Succesful')
            ->latest()
            ->first();
        $this->assertNotNull($cashNotif);
        $this->assertEquals('Cash Payment Succesful', $cashNotif->title);

        // Create pending GCash payment and verify
        $payment = Payment::create([
            'payment_code' => 'PAY-TEST-' . uniqid(),
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => 7,
            'billing_year' => 2026,
            'amount' => 2000,
            'payment_method' => 'gcash',
            'payment_date' => '2026-07-10',
            'gcash_reference' => 'GCASH-REF-777',
            'status' => 'pending',
        ]);

        // Admin verifies the GCash payment
        $verifyResp = $this->actingAs($this->admin)->post(route('admin.payments.approve-gcash', $payment->id));
        $verifyResp->assertSessionHas('success');
        $this->assertStringContainsString('Gcash Payment Succesful', session('success'));

        // Verify notification for GCash after admin verifies
        $gcashNotif = AppNotification::where('user_id', $tenantUser->id)
            ->where('title', 'Gcash Payment Succesful')
            ->latest()
            ->first();
        $this->assertNotNull($gcashNotif);
        $this->assertEquals('Gcash Payment Succesful', $gcashNotif->title);
    }

    public function test_payment_time_recorded_and_displayed(): void
    {
        $tenantUser = User::create([
            'name' => 'Pay Time Tenant',
            'email' => 'paytime_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);

        $room = Room::first();

        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'full_name' => 'Pay Time Resident',
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09444444444',
            'room_id' => $room->id,
            'gender' => 'Female',
            'date_of_birth' => '1997-03-12',
            'move_in_date' => '2026-01-01',
            'status' => 'active',
            'address' => 'Time Test Street',
        ]);

        // Record payment with explicit payment_time
        $response = $this->actingAs($this->admin)->post(route('admin.payments.store'), [
            'tenant_id' => $tenant->id,
            'billing_month' => 8,
            'billing_year' => 2026,
            'amount' => 2500,
            'payment_method' => 'cash',
            'payment_date' => '2026-08-15',
            'payment_time' => '14:30',
            'remarks' => 'Recorded with explicit time',
        ]);

        $response->assertSessionHas('success');

        $payment = Payment::where('tenant_id', $tenant->id)->latest()->first();
        $this->assertNotNull($payment);
        $this->assertEquals('14:30:00', $payment->payment_time);
        $this->assertEquals('2:30 PM', $payment->formatted_payment_time);

        // Verify index view displays payment time
        $indexResp = $this->actingAs($this->admin)->get(route('admin.payments.index', ['search' => $payment->payment_code]));
        $indexResp->assertStatus(200);
        $indexResp->assertSee('2:30 PM');

        // Verify show view displays payment time
        $showResp = $this->actingAs($this->admin)->get(route('admin.payments.show', $payment->id));
        $showResp->assertStatus(200);
        $showResp->assertSee('Payment Time');
        $showResp->assertSee('2:30 PM');

        // Update payment with a new payment time
        $updateResp = $this->actingAs($this->admin)->put(route('admin.payments.update', $payment->id), [
            'amount' => 2500,
            'billing_month' => 8,
            'billing_year' => 2026,
            'payment_date' => '2026-08-15',
            'payment_time' => '16:45',
            'payment_method' => 'cash',
            'status' => 'paid',
            'edit_reason' => 'Corrected transaction time to 4:45 PM',
        ]);

        $updateResp->assertRedirect(route('admin.payments.show', $payment->id));
        $payment->refresh();
        $this->assertEquals('16:45:00', $payment->payment_time);
        $this->assertEquals('4:45 PM', $payment->formatted_payment_time);
    }

    public function test_dashboard_welcome_header_has_no_date_time_badge(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Welcome name badge is present
        $response->assertSee('Welcome ' . $this->admin->name . '!');

        // The date badge with bi-calendar-check is removed from beside the welcome header
        $response->assertDontSee('<i class="bi bi-calendar-check me-1"></i>', false);
    }

    public function test_admin_approving_cash_payment_sends_cash_notification_not_gcash(): void
    {
        $user = User::create([
            'name' => 'Cash Tenant User ' . uniqid(),
            'email' => 'cash_tenant_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);

        $room = Room::first();

        $tenant = Tenant::create([
            'user_id' => $user->id,
            'full_name' => 'Cash Tenant ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09123456780',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1996-01-01',
            'move_in_date' => now()->toDateString(),
            'status' => 'active',
            'address' => 'Test Address',
        ]);

        $payment = Payment::create([
            'payment_code' => 'PAY-CASH-TEST-' . uniqid(),
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => now()->month,
            'billing_year' => now()->year,
            'amount' => 3000,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.payments.show', $payment->id))
            ->post(route('admin.payments.approve', $payment->id));
        $response->assertRedirect(route('admin.payments.show', $payment->id));
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Cash Payment Succesful', session('success'));
        $this->assertStringNotContainsString('GCash Payment Succesful', session('success'));

        $notif = AppNotification::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($notif);
        $this->assertEquals('Cash Payment Succesful', $notif->title);
        $this->assertStringContainsString('Cash payment', $notif->message);
        $this->assertStringNotContainsString('GCash', $notif->message);
    }

    public function test_tenants_can_be_arranged_and_filtered_by_payment_status(): void
    {
        $room = Room::first();

        // 1. Tenant Who Needs To Pay (Unpaid)
        $u1 = User::create([
            'name' => 'Unpaid Tenant',
            'email' => 'unpaid_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);
        $t1 = Tenant::create([
            'user_id' => $u1->id,
            'full_name' => 'AAA Unpaid Tenant ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09111111111',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1995-01-01',
            'move_in_date' => now()->startOfMonth()->toDateString(),
            'status' => 'active',
            'address' => 'Test Address 1',
        ]);

        // 2. Tenant Paid
        $u2 = User::create([
            'name' => 'Paid Tenant',
            'email' => 'paid_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);
        $t2 = Tenant::create([
            'user_id' => $u2->id,
            'full_name' => 'BBB Paid Tenant ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09222222222',
            'room_id' => $room->id,
            'gender' => 'Female',
            'date_of_birth' => '1996-01-01',
            'move_in_date' => now()->startOfMonth()->toDateString(),
            'status' => 'active',
            'address' => 'Test Address 2',
        ]);
        Payment::create([
            'payment_code' => 'PAY-TEST-PAID-' . uniqid(),
            'tenant_id' => $t2->id,
            'room_id' => $room->id,
            'billing_month' => now()->month,
            'billing_year' => now()->year,
            'amount' => $room->monthly_rent,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        // 3. Tenant Pending
        $u3 = User::create([
            'name' => 'Pending Tenant',
            'email' => 'pend_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);
        $t3 = Tenant::create([
            'user_id' => $u3->id,
            'full_name' => 'CCC Pending Tenant ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09333333333',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1997-01-01',
            'move_in_date' => now()->startOfMonth()->toDateString(),
            'status' => 'active',
            'address' => 'Test Address 3',
        ]);
        Payment::create([
            'payment_code' => 'PAY-TEST-PEND-' . uniqid(),
            'tenant_id' => $t3->id,
            'room_id' => $room->id,
            'billing_month' => now()->month,
            'billing_year' => now()->year,
            'amount' => $room->monthly_rent,
            'payment_method' => 'gcash',
            'payment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Filter: to_pay (Who Pay)
        $respToPay = $this->actingAs($this->admin)->get(route('admin.tenants.index', ['payment_status' => 'to_pay']));
        $respToPay->assertStatus(200);
        $respToPay->assertSee($t1->full_name);
        $respToPay->assertDontSee($t2->full_name);

        // Filter: paid
        $respPaid = $this->actingAs($this->admin)->get(route('admin.tenants.index', ['payment_status' => 'paid']));
        $respPaid->assertStatus(200);
        $respPaid->assertSee($t2->full_name);
        $respPaid->assertDontSee($t1->full_name);

        // Filter: pending
        $respPend = $this->actingAs($this->admin)->get(route('admin.tenants.index', ['payment_status' => 'pending']));
        $respPend->assertStatus(200);
        $respPend->assertSee($t3->full_name);

        // Arranged view: Who Pay First priority
        $respAll = $this->actingAs($this->admin)->get(route('admin.tenants.index', ['arrange' => 'payment_status']));
        $respAll->assertStatus(200);
        $respAll->assertSee('Arrange: Who Pay First');
        $respAll->assertSee('Payment Status');
    }

    public function test_payments_page_has_status_quick_filter_tabs_and_grouped_select_options(): void
    {
        $room = Room::first();
        $user = User::create([
            'name' => 'To Pay Resident',
            'email' => 'topay_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);
        Tenant::create([
            'user_id' => $user->id,
            'full_name' => 'Resident Due ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09555555555',
            'room_id' => $room->id,
            'gender' => 'Male',
            'date_of_birth' => '1995-01-01',
            'move_in_date' => now()->startOfMonth()->toDateString(),
            'status' => 'active',
            'address' => 'Test Address',
        ]);

        $respPayments = $this->actingAs($this->admin)->get(route('admin.payments.index'));
        $respPayments->assertStatus(200);
        $respPayments->assertSee('Quick Filter:');
        $respPayments->assertSee('All Payments');
        $respPayments->assertSee('Pending');
        $respPayments->assertSee('Partial');
        $respPayments->assertSee('Rejected');
        $respPayments->assertSee('Paid / Verified');

        $respCreate = $this->actingAs($this->admin)->get(route('admin.payments.create'));
        $respCreate->assertStatus(200);
        $respCreate->assertSee('Tenants Who Need to Pay (Rent Due / Unpaid)');
    }

    public function test_professional_design_amber_pending_green_assigned_room_and_notification_elements(): void
    {
        $room = Room::first();
        $user = User::create([
            'name' => 'Design Test Tenant',
            'email' => 'design_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant',
            'account_status' => 'approved',
        ]);
        $tenant = Tenant::create([
            'user_id' => $user->id,
            'full_name' => 'Design Tenant ' . uniqid(),
            'tenant_code' => 'TEN-' . uniqid(),
            'contact_number' => '09777777777',
            'room_id' => $room->id,
            'gender' => 'Female',
            'date_of_birth' => '1998-04-12',
            'move_in_date' => now()->startOfMonth()->toDateString(),
            'status' => 'active',
            'address' => 'Design Street',
        ]);

        // 1. Admin Tenants Index contains btn-tab-pending, btn-tab-partial, and assigned-room-chip
        $respTenants = $this->actingAs($this->admin)->get(route('admin.tenants.index'));
        $respTenants->assertStatus(200);
        $respTenants->assertSee('btn-tab-pending');
        $respTenants->assertSee('btn-tab-partial');
        $respTenants->assertSee('assigned-room-chip');

        // 2. Admin Payments Index contains btn-tab-pending, btn-tab-partial
        $respPayments = $this->actingAs($this->admin)->get(route('admin.payments.index'));
        $respPayments->assertStatus(200);
        $respPayments->assertSee('btn-tab-pending');
        $respPayments->assertSee('btn-tab-partial');

        // 3. Tenant Dashboard Assigned Room card uses accent-green
        $respDash = $this->actingAs($user)->get(route('tenant.dashboard'));
        $respDash->assertStatus(200);
        $respDash->assertSee('accent-green');
        $respDash->assertSee('Assigned Room');

        // 4. Admin Tenant Show page contains assigned-room-chip
        $respTenantShow = $this->actingAs($this->admin)->get(route('admin.tenants.show', $tenant->id));
        $respTenantShow->assertStatus(200);
        $respTenantShow->assertSee('assigned-room-chip');
    }
}
