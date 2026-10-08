<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomRequest;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Default System Settings
        Setting::set('gcash_name', 'Admin');
        Setting::set('gcash_number', '0917-888-9999');
        Setting::set('gcash_qr_path', 'settings/default-gcash-qr.svg');

        // 1. Create or Update Admin Users
        $adminData = [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'account_status' => 'approved',
            'contact_number' => '0917-888-9999',
            'address' => '123 Sampaguita Street, Barangay San Antonio, Quezon City',
            'gender' => 'female',
            'profile_picture' => 'avatars/admin.svg',
            'date_of_birth' => '1982-05-15',
        ];

        $admin = User::updateOrCreate(['email' => 'admin@boardinghouse.local'], $adminData);
        User::updateOrCreate(['email' => 'admin@gmail.com'], $adminData);

        // Default test tenant user
        User::updateOrCreate(
            ['email' => 'tenant@boardinghouse.local'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'account_status' => 'approved',
                'contact_number' => '0917-111-2233',
                'address' => 'Barangay Poblacion, Malolos, Bulacan',
                'gender' => 'male',
                'profile_picture' => 'avatars/juan-dela-cruz.svg',
                'date_of_birth' => '2001-08-12',
            ]
        );

        // 2. Create Rooms
        $roomsData = [
            [
                'room_number' => '101',
                'floor' => 1,
                'room_type' => 'Single Deluxe',
                'capacity' => 1,
                'monthly_rent' => 3500.00,
                'description' => "Spacious ground-floor single deluxe room with a private en-suite bathroom, inverter air conditioner, and study desk. Ideal for quiet study or work-from-home tenants.",
                'amenities' => ['Inverter Air Conditioner', 'Private Toilet & Bath', 'Single Bed & Spring Mattress', 'Study Table & Chair', 'Wardrobe Cabinet', 'High-Speed Wi-Fi'],
                'manual_available' => true,
            ],
            [
                'room_number' => '102',
                'floor' => 1,
                'room_type' => 'Double Sharing',
                'capacity' => 2,
                'monthly_rent' => 2500.00,
                'description' => "Bright and well-ventilated double occupancy room located near the main entrance and communal kitchen. Features sturdy double-deck bunk beds.",
                'amenities' => ['Ceiling Fan', 'Bunk Bed with Foam', 'Individual Lockers', 'Shared Bathroom Access', 'Wi-Fi'],
                'manual_available' => true,
            ],
            [
                'room_number' => '103',
                'floor' => 1,
                'room_type' => 'Single Standard',
                'capacity' => 1,
                'monthly_rent' => 2800.00,
                'description' => "Comfortable single room on the first floor with direct access to the side garden patio. Great natural air circulation.",
                'amenities' => ['Electric Wall Fan', 'Single Bed', 'Study Desk', 'Shared Bathroom', 'Wi-Fi'],
                'manual_available' => true,
            ],
            [
                'room_number' => '201',
                'floor' => 2,
                'room_type' => 'Quad Dormitory',
                'capacity' => 4,
                'monthly_rent' => 2000.00,
                'description' => "Four-person dormitory style room on the second floor. Budget-friendly for college students and interns with private bathroom.",
                'amenities' => ['2 Heavy-duty Bunk Beds', 'Individual Steel Lockers', 'Ensuite Bathroom', 'Air Conditioning', 'Wi-Fi'],
                'manual_available' => true,
            ],
            [
                'room_number' => '202',
                'floor' => 2,
                'room_type' => 'Double Deluxe',
                'capacity' => 2,
                'monthly_rent' => 3200.00,
                'description' => "Deluxe twin room on floor 2 with private sliding door access to the second-floor balcony. Includes air conditioning and private bathroom.",
                'amenities' => ['Air Conditioning', 'Twin Beds', 'Balcony Access', 'Private Toilet & Bath', 'Closet Storage', 'Wi-Fi'],
                'manual_available' => true,
            ],
            [
                'room_number' => '203',
                'floor' => 2,
                'room_type' => 'Single Standard',
                'capacity' => 1,
                'monthly_rent' => 2900.00,
                'description' => "Second-floor single room with wide windows offering pleasant street views and sunlight. Quiet study environment.",
                'amenities' => ['Wall Fan', 'Single Bed', 'Writing Desk', 'Shared Bathroom', 'Wi-Fi'],
                'manual_available' => true,
            ],
            [
                'room_number' => '301',
                'floor' => 3,
                'room_type' => 'Studio Suite',
                'capacity' => 2,
                'monthly_rent' => 4500.00,
                'description' => "Top-floor penthouse studio suite with scenic panoramic view, private kitchenette with sink, hot and cold shower, and inverter AC.",
                'amenities' => ['Inverter AC', 'Queen Size Bed', 'Private Bath with Hot Shower', 'Kitchenette Sink', 'Mini Refrigerator', 'Balcony', 'Fiber Wi-Fi'],
                'manual_available' => true,
            ],
            [
                'room_number' => '302',
                'floor' => 3,
                'room_type' => 'Double Standard',
                'capacity' => 2,
                'monthly_rent' => 2600.00,
                'description' => "Third-floor shared room with high ceilings. Very quiet atmosphere suitable for board exam reviewers and medical students.",
                'amenities' => ['Ceiling Fan', 'Bunk Bed', 'Individual Wardrobes', 'Shared Bathroom', 'Wi-Fi'],
                'manual_available' => true,
            ],
        ];

        $rooms = [];
        foreach ($roomsData as $rData) {
            $room = Room::firstOrCreate(['room_number' => $rData['room_number']], $rData);
            $rooms[$room->room_number] = $room;

            // Attach sample primary image
            RoomImage::firstOrCreate([
                'room_id' => $room->id,
                'image_path' => 'rooms/sample-room-1.svg',
            ], [
                'is_primary' => true,
            ]);

            // Attach secondary gallery image
            RoomImage::firstOrCreate([
                'room_id' => $room->id,
                'image_path' => 'rooms/sample-room-2.svg',
            ], [
                'is_primary' => false,
            ]);
        }

        // 3. Approved & Active Tenants
        // Tenant 1: Juan Dela Cruz (Assigned Room 101, Move-in August 15)
        $userJuan = User::firstOrCreate(['email' => 'juan@example.com'], [
            'name' => 'Juan Dela Cruz',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'approved',
            'contact_number' => '0917-111-2233',
            'address' => 'Barangay Poblacion, Malolos, Bulacan',
            'gender' => 'male',
            'profile_picture' => 'avatars/juan-dela-cruz.svg',
            'date_of_birth' => '2001-08-12',
        ]);

        $tenantJuan = Tenant::firstOrCreate(['user_id' => $userJuan->id], [
            'room_id' => $rooms['101']->id,
            'tenant_code' => 'T-2026-0001',
            'profile_picture' => 'avatars/juan-dela-cruz.svg',
            'move_in_date' => '2026-08-15',
            'status' => 'active',
            'emergency_contact_name' => 'Pedro Dela Cruz (Father)',
            'emergency_contact_number' => '0918-111-2222',
            'notes' => 'Punctual with rent. IT working professional.',
        ]);

        // Tenant 2: Maria Santos (Assigned Room 102, Move-in September 01)
        $userMaria = User::firstOrCreate(['email' => 'maria@example.com'], [
            'name' => 'Maria Santos',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'approved',
            'contact_number' => '0920-222-3344',
            'address' => 'San Fernando, Pampanga',
            'gender' => 'female',
            'profile_picture' => 'avatars/maria-santos.svg',
            'date_of_birth' => '2002-11-20',
        ]);

        $tenantMaria = Tenant::firstOrCreate(['user_id' => $userMaria->id], [
            'room_id' => $rooms['102']->id,
            'tenant_code' => 'T-2026-0002',
            'profile_picture' => 'avatars/maria-santos.svg',
            'move_in_date' => '2026-09-01',
            'status' => 'active',
            'emergency_contact_name' => 'Elena Santos (Mother)',
            'emergency_contact_number' => '0919-333-4444',
            'notes' => 'Nursing student.',
        ]);

        // Tenant 3: Mark Reyes (Assigned Room 202, Move-in September 10)
        $userMark = User::firstOrCreate(['email' => 'mark@example.com'], [
            'name' => 'Mark Reyes',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'approved',
            'contact_number' => '0915-333-5566',
            'address' => 'Antipolo City, Rizal',
            'gender' => 'male',
            'profile_picture' => 'avatars/mark-reyes.svg',
            'date_of_birth' => '2000-03-05',
        ]);

        $tenantMark = Tenant::firstOrCreate(['user_id' => $userMark->id], [
            'room_id' => $rooms['202']->id,
            'tenant_code' => 'T-2026-0003',
            'profile_picture' => 'avatars/mark-reyes.svg',
            'move_in_date' => '2026-09-10',
            'status' => 'active',
            'emergency_contact_name' => 'Arturo Reyes (Brother)',
            'emergency_contact_number' => '0922-444-5555',
            'notes' => 'Civil engineering board reviewer.',
        ]);

        // Tenant 4: Ana Lim (Assigned Room 201, Move-in July 05, Overdue rent scenario)
        $userAna = User::firstOrCreate(['email' => 'ana@example.com'], [
            'name' => 'Ana Lim',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'approved',
            'contact_number' => '0928-444-6677',
            'address' => 'Lucena City, Quezon Province',
            'gender' => 'female',
            'profile_picture' => 'avatars/ana-lim.svg',
            'date_of_birth' => '2003-04-18',
        ]);

        $tenantAna = Tenant::firstOrCreate(['user_id' => $userAna->id], [
            'room_id' => $rooms['201']->id,
            'tenant_code' => 'T-2026-0004',
            'profile_picture' => 'avatars/ana-lim.svg',
            'move_in_date' => '2026-07-05',
            'status' => 'active',
            'emergency_contact_name' => 'Grace Lim (Aunt)',
            'emergency_contact_number' => '0917-777-8888',
            'notes' => 'Medical laboratory technician student.',
        ]);

        // 4. Pending Tenant Applicant (Carlo Gomez, requested Room 203)
        $userCarlo = User::firstOrCreate(['email' => 'carlo@example.com'], [
            'name' => 'Carlo Gomez',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'pending',
            'contact_number' => '0917-555-6677',
            'address' => 'Baguio City, Benguet',
            'gender' => 'male',
            'profile_picture' => 'avatars/carlo-gomez.svg',
            'date_of_birth' => '2002-09-14',
        ]);

        $tenantCarlo = Tenant::firstOrCreate(['user_id' => $userCarlo->id], [
            'room_id' => null,
            'tenant_code' => 'T-2026-0005',
            'profile_picture' => 'avatars/carlo-gomez.svg',
            'move_in_date' => '2026-10-15',
            'status' => 'pending',
            'emergency_contact_name' => 'Mario Gomez (Father)',
            'emergency_contact_number' => '0917-111-2233',
            'notes' => 'Transferring near university campus. Quiet non-smoker.',
        ]);

        RoomRequest::firstOrCreate(['user_id' => $userCarlo->id, 'tenant_id' => $tenantCarlo->id], [
            'room_id' => $rooms['203']->id,
            'preferred_move_in_date' => '2026-10-15',
            'notes' => 'Transferring near university campus. Quiet non-smoker.',
            'status' => 'pending',
        ]);

        // 5. Rejected Tenant Applicant (Liza Soberano)
        $userLiza = User::firstOrCreate(['email' => 'liza@example.com'], [
            'name' => 'Liza Soberano',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'account_status' => 'rejected',
            'rejection_reason' => 'Requested Room 101 is already fully occupied. All current single rooms are reserved for the current semester.',
            'contact_number' => '0918-999-0011',
            'address' => 'Cebu City, Cebu',
            'gender' => 'female',
            'profile_picture' => 'avatars/liza-soberano.svg',
            'date_of_birth' => '2001-01-04',
        ]);

        $tenantLiza = Tenant::firstOrCreate(['user_id' => $userLiza->id], [
            'room_id' => null,
            'tenant_code' => 'T-2026-0006',
            'profile_picture' => 'avatars/liza-soberano.svg',
            'move_in_date' => null,
            'status' => 'inactive',
            'notes' => 'Application rejected due to full capacity.',
        ]);

        // 6. Payments

        // Juan Dela Cruz:
        // September 2026 Rent - Verified GCash
        Payment::create([
            'tenant_id' => $tenantJuan->id,
            'room_id' => $rooms['101']->id,
            'amount' => 3500.00,
            'billing_month' => 9,
            'billing_year' => 2026,
            'payment_method' => 'gcash',
            'payment_date' => '2026-09-14',
            'gcash_reference' => '1002938472910',
            'receipt_path' => 'receipts/sample-gcash-receipt.svg',
            'notes' => 'Early payment for September.',
            'status' => 'verified',
            'verified_at' => '2026-09-15 09:00:00',
            'verified_by' => $admin->id,
        ]);

        // October 2026 Rent - Pending GCash Verification
        Payment::create([
            'tenant_id' => $tenantJuan->id,
            'room_id' => $rooms['101']->id,
            'amount' => 3500.00,
            'billing_month' => 10,
            'billing_year' => 2026,
            'payment_method' => 'gcash',
            'payment_date' => '2026-10-04',
            'gcash_reference' => '1003882741923',
            'receipt_path' => 'receipts/sample-gcash-receipt.svg',
            'notes' => 'October rent transfer via GCash Express Send.',
            'status' => 'pending',
        ]);

        // Maria Santos:
        // September 2026 Rent - Verified Cash
        Payment::create([
            'tenant_id' => $tenantMaria->id,
            'room_id' => $rooms['102']->id,
            'amount' => 2500.00,
            'billing_month' => 9,
            'billing_year' => 2026,
            'payment_method' => 'cash',
            'payment_date' => '2026-09-01',
            'notes' => 'Paid in cash at boarding house office.',
            'status' => 'verified',
            'verified_at' => '2026-09-01 10:00:00',
            'verified_by' => $admin->id,
        ]);

        // October 2026 Rent - Verified Cash
        Payment::create([
            'tenant_id' => $tenantMaria->id,
            'room_id' => $rooms['102']->id,
            'amount' => 2500.00,
            'billing_month' => 10,
            'billing_year' => 2026,
            'payment_method' => 'cash',
            'payment_date' => '2026-10-01',
            'notes' => 'Handed cash to Landlady.',
            'status' => 'verified',
            'verified_at' => '2026-10-01 11:30:00',
            'verified_by' => $admin->id,
        ]);

        // Mark Reyes:
        // September 2026 Rent - Verified GCash
        Payment::create([
            'tenant_id' => $tenantMark->id,
            'room_id' => $rooms['202']->id,
            'amount' => 3200.00,
            'billing_month' => 9,
            'billing_year' => 2026,
            'payment_method' => 'gcash',
            'payment_date' => '2026-09-10',
            'gcash_reference' => '1004928374829',
            'receipt_path' => 'receipts/sample-gcash-receipt.svg',
            'status' => 'verified',
            'verified_at' => '2026-09-10 14:00:00',
            'verified_by' => $admin->id,
        ]);

        // Ana Lim:
        // September 2026 Rent - Verified Cash
        Payment::create([
            'tenant_id' => $tenantAna->id,
            'room_id' => $rooms['201']->id,
            'amount' => 2000.00,
            'billing_month' => 9,
            'billing_year' => 2026,
            'payment_method' => 'cash',
            'payment_date' => '2026-09-05',
            'notes' => 'September rent paid in full.',
            'status' => 'verified',
            'verified_at' => '2026-09-05 16:00:00',
            'verified_by' => $admin->id,
        ]);
        // October 2026 Rent: Intentionally unpaid -> Ana Lim is past her due date (October 5 or before) -> Overdue!

        // 7. Maintenance Requests
        MaintenanceRequest::create([
            'tenant_id' => $tenantJuan->id,
            'room_id' => $rooms['101']->id,
            'category' => 'Plumbing',
            'priority' => 'medium',
            'title' => 'Bathroom sink faucet dripping slowly',
            'description' => 'The hot/cold mixer faucet in Room 101 private bathroom continues to drip after shutting off tightly. Please check washer.',
            'status' => 'in_progress',
            'assigned_to' => 'Kuya Eddie (Plumber)',
            'target_date' => '2026-10-06',
            'admin_remarks' => 'Plumber purchased replacement O-ring seals. Will install on Monday morning.',
        ]);

        MaintenanceRequest::create([
            'tenant_id' => $tenantMaria->id,
            'room_id' => $rooms['102']->id,
            'category' => 'Electrical',
            'priority' => 'urgent',
            'title' => 'Outlet near study desk sparking',
            'description' => 'Small sparks visible when plugging in phone charger. Switched off breaker for that outlet temporarily.',
            'status' => 'resolved',
            'assigned_to' => 'Mang Boy (Electrician)',
            'target_date' => '2026-09-28',
            'admin_remarks' => 'Replaced electrical receptacle box and re-tightened copper wires. Tested with multimeter, fully safe.',
            'resolved_at' => '2026-09-28 15:30:00',
        ]);

        MaintenanceRequest::create([
            'tenant_id' => $tenantAna->id,
            'room_id' => $rooms['201']->id,
            'category' => 'Structural',
            'priority' => 'low',
            'title' => 'Screen window latch loose',
            'description' => 'The screen frame on the right side window vibrates when there is wind. Requires screw tightening.',
            'status' => 'pending',
            'assigned_to' => null,
            'target_date' => null,
            'admin_remarks' => null,
        ]);

        // 8. Room Requests (Juan requests transfer to Studio 301)
        RoomRequest::create([
            'user_id' => $userJuan->id,
            'room_id' => $rooms['301']->id,
            'preferred_move_in_date' => '2026-11-01',
            'notes' => 'Inquiring if Room 301 studio suite is available starting November for upgrade.',
            'status' => 'pending',
        ]);

        // 9. In-App Notifications
        AppNotification::create([
            'user_id' => $admin->id,
            'title' => 'New GCash Payment for Verification',
            'message' => 'Tenant Juan Dela Cruz submitted ₱3,500.00 GCash payment for October 2026 (Ref: 1003882741923).',
            'action_url' => route('admin.payments.index'),
            'type' => 'payment',
            'is_read' => false,
        ]);

        AppNotification::create([
            'user_id' => $admin->id,
            'title' => 'New Pending Registration',
            'message' => 'Carlo Gomez registered for an account and requested Room 203.',
            'action_url' => route('admin.pending-tenants.index'),
            'type' => 'tenant',
            'is_read' => false,
        ]);

        AppNotification::create([
            'user_id' => $admin->id,
            'title' => 'New Maintenance Request',
            'message' => 'Screen window latch loose reported for Room 201.',
            'action_url' => route('admin.maintenance.index'),
            'type' => 'maintenance',
            'is_read' => false,
        ]);

        AppNotification::create([
            'user_id' => $userJuan->id,
            'title' => 'Rent Payment Verified',
            'message' => 'Your rent payment for September 2026 has been verified by the Landlady. Thank you!',
            'action_url' => route('tenant.payments.index'),
            'type' => 'payment',
            'is_read' => true,
        ]);
    }
}
