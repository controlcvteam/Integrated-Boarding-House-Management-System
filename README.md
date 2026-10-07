# Integrated Boarding House Management System (IBHMS)

A complete, production-ready, full-stack web application designed for boarding house management where the **Landlady is the Admin** and the residents renting rooms are **Tenants**.

Built entirely from scratch using **Laravel 11, PHP 8.2+, MySQL (MariaDB), Laravel Blade, CSS, and Vanilla JavaScript**.

---

## 🌟 Key Highlights & Architecture

- **Backend**: Laravel 11 with Eloquent ORM, Migrations, Seeders, Form Requests & Validation, Middleware, and Storage.
- **Database**: MySQL / MariaDB (running on `127.0.0.1:3306`, Database: `boarding_house`).
- **Frontend**: Laravel Blade templates, responsive CSS design system (wireframe-faithful aesthetics), Bootstrap 5 utility classes, and Vanilla JavaScript.
- **Theme**: Seamless **Light Mode** and **Dark Mode** toggle persisting across sessions via `localStorage`.
- **Zero SPAs**: No React, Vue, Inertia, Next.js, or TypeScript. Clean, reliable server-rendered Blade application.
- **Automated Test Suite**: 17 tests (47 assertions) validating business rules, authentication security, role separation, privacy guards, payments, and rent calculations.

---

## 👥 User Roles & Access Control

### 1. Landlady (Admin)
- **Interactive Operations Dashboard**: High-level KPI summary cards for pending applicants, active tenants, room occupancy, expected collections, outstanding rent, overdue accounts, and open maintenance requests.
- **Rent Due Calendar**: Interactive monthly calendar with color-coded due dates (Green = Paid, Yellow = Pending GCash Verification, Red = Due / Overdue). Clicking any event reveals tenant details with an instant *"Record Cash Payment (Mark as Paid)"* modal.
- **Pending Registrations Management**: Review tenant applicants, assign final room and move-in date upon approval, or reject with a formal explanation.
- **Tenant Management**: 4-tab details screen (Personal Profile, Lease Agreement with Room Transfer and Move-Out actions, Payment History, and Notes/Remarks). Full CRUD with confirmation dialogs.
- **Room Management**: Room inventory, multi-photo uploads, primary image selector, manual availability override, and dynamic capacity tracking (`capacity - activeTenants`).
- **Payments Management (Cash & GCash Only)**:
  - **Cash**: Direct cash recording by Landlady with auto-calculated status (`verified` or `partial`).
  - **GCash Verification**: Review uploaded GCash receipt screenshot, verify reference number and billing cycle, approve with one click or reject with rejection remarks.
  - **Receipt Printing**: Printable, professional boarding house official receipts.
- **Maintenance Management**: Assign technicians/staff, set target completion dates, update status progression (Pending $\rightarrow$ In Progress $\rightarrow$ Resolved / Rejected).
- **Reports & Financial Analytics**: Print-friendly analytical reports for Monthly Collections, Outstanding Rent, Occupancy & Revenue Yield, Tenant Directory, and Maintenance.
- **Settings**: Update Landlady contact information and security password.

### 2. Tenant
- **Public Self-Registration**: Apply online with personal details and optional preferred room. Accounts are strictly created with `role = tenant` and `account_status = pending`. (Public admin registration is strictly prohibited).
- **Approval Gate**: Pending applicants are directed to a dedicated Pending Status page with room browsing access. Rejected applicants see the Landlady's rejection reason. Only approved tenants can access the Tenant Portal.
- **Tenant Dashboard**: View assigned room specs, monthly rent, and current month payment status (Paid, In Verification, Payment Due, or Overdue with exact due date).
- **Available Rooms Browsing**: Browse boarding house rooms, floor levels, capacities, and amenities. **Strict Privacy Rule**: Other tenants' names, identities, or contact details are never exposed.
- **My Room**: Complete view of assigned room, amenities, lease agreement details, monthly due date, and emergency contact on file.
- **GCash Payment Submission**: Choose billing month/year, enter GCash reference number, and upload proof-of-payment receipt screenshot for Landlady verification.
- **Payment History & Receipts**: Access complete historical ledger of cash and GCash payments with official printable receipts.
- **Maintenance Reporting**: Submit repair requests for plumbing, electrical, furniture, or structural issues with photo attachments and track live resolution progress.
- **Settings**: Update mobile number, emergency contact details, and account password.

---

## 🔑 Demo Accounts & Pre-Seeded Credentials

All passwords are set to: `password`

| Role | Account Name | Email Address | Password | Scenario / Status |
| :--- | :--- | :--- | :--- | :--- |
| **Landlady (Admin)** | Maria Clara | `admin@boardinghouse.local` | `password` | Full administrative control |
| **Approved Tenant** | Juan Dela Cruz | `juan@example.com` | `password` | Room 101, October GCash payment pending verification |
| **Approved Tenant** | Maria Santos | `maria@example.com` | `password` | Room 102, Settled via Cash |
| **Approved Tenant** | Mark Reyes | `mark@example.com` | `password` | Room 202, Active tenant |
| **Approved Tenant** | Ana Lim | `ana@example.com` | `password` | Room 201, **Overdue Rent** (Due on 5th of each month) |
| **Pending Applicant** | Carlo Gomez | `carlo@example.com` | `password` | Pending Landlady review for Room 203 |
| **Rejected Applicant** | Liza Soberano | `liza@example.com` | `password` | Rejected with formal explanation |

---

## 📐 Business Rules & Logic

1. **Rent Due Date Calculation**:
   - Rent due date is determined strictly by the tenant's `move_in_date` day (not the registration date).
   - If a tenant moves in on the 15th, rent is due on the 15th of each month.
   - **Month-End Handling**: For move-in dates on 29, 30, or 31, shorter months automatically adjust to the final day of that month (e.g. February 28 or 29 in leap years).
2. **Payment Methods**:
   - Strictly limited to `cash` and `gcash`. No check, credit card, bank transfer, or crypto.
   - Cash payments are entered by the Landlady and marked `verified` (or `partial` if below full rent).
   - GCash payments are submitted by tenants with receipt screenshots and require Landlady verification.
3. **Room Availability**:
   - A room is available only when `manual_available = true` AND `activeTenants < capacity`.
4. **Occupant Privacy**:
   - Landlady has full visibility over all boarders.
   - Public and tenant room browsing shows only available slot counts and amenities—never occupants' personal details.

---

## 🚀 Setup & Installation Guide

### Prerequisites
- PHP 8.2 or higher
- MySQL / MariaDB (running on `127.0.0.1:3306`)
- Composer
- Web browser (Chrome, Firefox, Edge, Safari)

### 1. Database Configuration
Ensure MySQL/MariaDB is running on port 3306 and verify `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boarding_house
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Database Migrations & Seeding
Run the clean migration and seed all sample rooms, tenants, payments, and tickets:

```bash
php artisan migrate:fresh --seed
```

### 3. Storage Symlink
Ensure the public storage symlink is active for room images and receipt screenshots:

```bash
php artisan storage:link
```

### 4. Run the Automated Test Suite
Verify that all 17 feature and unit tests pass:

```bash
php artisan test
```

### 5. Launch the Local Development Server
Start the Laravel local server:

```bash
php artisan serve
```

Visit the application in your browser at: **`http://127.0.0.1:8000`**

---

## 🧪 Testing Checklist

- [x] **Admin Login**: Log in as `admin@boardinghouse.local` $\rightarrow$ redirected to `/admin/dashboard`.
- [x] **Calendar Interaction**: Click on any tenant badge in the Rent Due Calendar $\rightarrow$ details modal opens $\rightarrow$ click *"Record Cash Payment"* to quickly settle rent.
- [x] **Verify GCash Payment**: Navigate to *Payments* $\rightarrow$ click on Juan Dela Cruz's pending payment $\rightarrow$ preview uploaded receipt $\rightarrow$ click *Verify & Approve*.
- [x] **Review Pending Applicant**: Navigate to *Pending Accounts* $\rightarrow$ review Carlo Gomez $\rightarrow$ assign Room 203 and move-in date $\rightarrow$ approve.
- [x] **Tenant Login**: Log in as `juan@example.com` $\rightarrow$ view assigned room, rent due status, payment history, and receipt.
- [x] **Submit GCash Rent**: Go to *Payments* $\rightarrow$ *Submit Rent via GCash* $\rightarrow$ fill reference number and attach screenshot $\rightarrow$ submit.
- [x] **Report Maintenance**: Go to *Maintenance* $\rightarrow$ *Report New Issue* $\rightarrow$ select category, urgency, and submit.
- [x] **Pending Access Guard**: Log in as `carlo@example.com` $\rightarrow$ redirected to `/account/pending`. Attempting to access `/tenant/dashboard` is blocked.
- [x] **Rejected Access Guard**: Log in as `liza@example.com` $\rightarrow$ redirected to `/account/rejected` with the Landlady's rejection reason.
- [x] **Theme Switcher**: Click the theme toggle icon in the navbar $\rightarrow$ switches smoothly between Light and Dark mode across all screens.
