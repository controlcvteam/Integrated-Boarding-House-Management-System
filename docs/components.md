Components

## 1. Overview

The Integrated Boarding House Management System is a web-based application intended to organize boarding house operations, including tenant registration, room selection, room management, tenant records, and rental payment tracking. This document identifies reusable user interface (UI) components and maps them to the screens they support. Components will be reused to maintain a consistent layout and reduce duplicate code.

**Scope note:** The publicly accessible login and registration pages confirm the sign-in form, tenant registration, room selection, room filtering, and pending administrator approval. Other dashboards and management pages below are **planned lab components** and must be checked against the team's approved wireframes; their presence in the deployed application has not been verified.

## 2. Reusable Components

| No. | Component | Description | Used In |
|---|---|---|---|
| 1 | AppHeader | Displays application branding and page heading. | Login, Registration, Dashboards |
| 2 | NavigationSidebar | Provides navigation links between management modules. | Admin Dashboard, Management Pages, Tenant Dashboard |
| 3 | TopNavigation | Displays screen title and account controls. | Dashboards, Management Pages |
| 4 | PageTitle | Shows the current screen title and description. | All main screens |
| 5 | FormField | Displays a labeled text, number, email, date, or password input. | Login, Registration, Create/Edit Forms |
| 6 | SelectField | Provides a dropdown for selecting predefined options. | Registration, Room Forms, Filters |
| 7 | PrimaryButton | Submits a form or performs the main action. | Login, Registration, Create/Edit Forms |
| 8 | SecondaryButton | Handles cancel, back, and alternative actions. | Forms, Detail Views |
| 9 | SearchBar | Filters rooms, tenants, or records based on keywords. | Registration, Room Management, Tenant Management |
| 10 | FilterControls | Filters records by status, room type, or date. | Room Management, Tenant Management, Payments |
| 11 | RoomCard | Shows room number, type, floor, capacity, and price. | Registration, Room Catalog, Room Management |
| 12 | RoomSelection | Allows a prospective tenant to choose a preferred room or decide later. | Registration |
| 13 | SummaryCard | Displays a key metric with a label and value. | Admin Dashboard, Tenant Dashboard |
| 14 | DataTable | Displays structured records in rows and columns. | Room Management, Tenant Management, Payments |
| 15 | StatusBadge | Highlights states such as Pending, Approved, Available, Occupied, Paid, and Unpaid. | Registration Review, Room Management, Tenant Management, Payments |
| 16 | ActionMenu | Provides View, Edit, or other available record actions. | Management Tables |
| 17 | DetailCard | Displays complete information about a selected record. | Room Details, Tenant Details, Payment Details |
| 18 | ConfirmationDialog | Requests confirmation before a potentially destructive action. | Management Pages |
| 19 | NotificationMessage | Displays success, warning, or information messages. | Forms, Dashboards, Management Pages |
| 20 | EmptyState | Explains when a list has no matching records. | Room Catalog, Room Management, Tenant Management, Payments |
| 21 | LoadingState | Displays a loading message, spinner, or placeholder while data loads. | All Data-Driven Screens |
| 22 | ErrorState | Displays a useful error and, where appropriate, a retry action. | Forms, Data-Driven Screens |
| 23 | Pagination | Allows users to navigate across multiple pages of records. | Management Tables |
| 24 | AccountMenu | Provides account-related options such as logout. | Authenticated Screens |
| 25 | PaymentSummary | Displays rental balance, due date, or payment information. | Payment Management, Tenant Dashboard |

## 3. Screen-to-Component Mapping

### 3.1 Login Page

**Purpose:** Allow registered administrators and tenants to sign in to their accounts.

**Components:**
- AppHeader
- PageTitle
- FormField — Email Address
- FormField — Password
- FormField — Remember Me checkbox
- PrimaryButton — Sign In
- SecondaryButton or Link — Register as Tenant
- NotificationMessage
- LoadingState
- ErrorState

**States:** Default, submitting/loading, invalid credentials/error.

### 3.2 Tenant Registration Page

**Purpose:** Allow new tenants to enter personal details, choose a preferred room, and submit an account request for administrator approval.

**Components:**
- AppHeader
- PageTitle
- FormField — Full Name
- FormField — Email Address
- FormField — Contact Number
- FormField — Complete Home Address
- SelectField — Gender
- FormField — Date of Birth
- SearchBar — Room filter
- RoomCard
- RoomSelection — Preferred room or Decide Later
- SelectField — Room selection list
- FormField — Password
- FormField — Confirm Password
- PrimaryButton — Register Account
- SecondaryButton or Link — Back to Login
- NotificationMessage
- EmptyState
- LoadingState
- ErrorState

**States:** Default, no matching rooms, registration submitting, validation error, successful submission/pending approval.

### 3.3 Room Catalog Page

**Purpose:** Let prospective tenants browse room details before selecting a room.

**Components:**
- AppHeader
- PageTitle
- SearchBar
- FilterControls
- RoomCard
- StatusBadge
- DetailCard
- EmptyState
- LoadingState
- ErrorState

**States:** Available room listings, no matching rooms, loading, data error.

### 3.4 Admin Dashboard — Planned View

**Purpose:** Present an overview of boarding house operations to administrators.

**Components:**
- NavigationSidebar
- TopNavigation
- AccountMenu
- PageTitle
- SummaryCard — Total Rooms
- SummaryCard — Available Rooms
- SummaryCard — Occupied Rooms
- SummaryCard — Registered Tenants
- SummaryCard — Pending Registrations
- SummaryCard — Payment Overview (if included in wireframes)
- DataTable — Recent records or pending requests
- StatusBadge
- LoadingState
- EmptyState
- ErrorState

**States:** Dashboard loaded with sample information, loading, empty records, error.

### 3.5 Room Management — Planned View

**Purpose:** Let an administrator view and maintain boarding house room information.

**Components:**
- NavigationSidebar
- TopNavigation
- PageTitle
- SearchBar
- FilterControls
- DataTable or RoomCard list
- StatusBadge
- PrimaryButton — Add Room
- ActionMenu — View/Edit
- Pagination
- ConfirmationDialog (if deletion is permitted)
- NotificationMessage
- EmptyState
- LoadingState
- ErrorState

**States:** Room list, no rooms/no matches, loading, error.

### 3.6 Add/Edit Room Form — Planned View

**Purpose:** Create or update room information.

**Components:**
- NavigationSidebar
- TopNavigation
- PageTitle
- FormField — Room Number
- SelectField — Room Type
- FormField — Floor
- FormField — Capacity/Slots
- FormField — Monthly Rate
- SelectField — Room Status
- PrimaryButton — Save Room
- SecondaryButton — Cancel
- NotificationMessage
- LoadingState
- ErrorState

**States:** Blank form, prefilled edit form, validation error, saving, saved successfully.

### 3.7 Tenant Management — Planned View

**Purpose:** View tenant records and review registration requests.

**Components:**
- NavigationSidebar
- TopNavigation
- PageTitle
- SearchBar
- FilterControls
- DataTable — Tenant Name, Contact, Room, Account Status
- StatusBadge — Pending/Approved/Rejected (as supported by the system)
- ActionMenu — View/Edit/Review
- DetailCard
- Pagination
- ConfirmationDialog
- NotificationMessage
- EmptyState
- LoadingState
- ErrorState

**States:** Tenant list, pending registrations, no tenants, loading, error.

### 3.8 Payment Management — Planned View

**Purpose:** Display and manage rental payment information, if this feature is part of the approved system scope.

**Components:**
- NavigationSidebar
- TopNavigation
- PageTitle
- PaymentSummary
- SearchBar
- FilterControls
- DataTable — Tenant, Room, Amount, Due Date, Payment Date, Status
- StatusBadge — Paid/Pending/Overdue (as applicable)
- DetailCard
- Pagination
- NotificationMessage
- EmptyState
- LoadingState
- ErrorState

**States:** Payment records, no payments, loading, error.

### 3.9 Tenant Dashboard — Planned View

**Purpose:** Let authenticated tenants view their room and account information.

**Components:**
- NavigationSidebar or TopNavigation
- AccountMenu
- PageTitle
- SummaryCard — Assigned Room
- DetailCard — Room Information
- PaymentSummary — Rental Status (if available)
- DataTable — Payment History (if available)
- StatusBadge
- NotificationMessage
- EmptyState
- LoadingState
- ErrorState

**States:** Assigned room, no assigned room, records loading, error.

## 4. Component Reuse Matrix

| Component | Login | Register | Room Catalog | Admin | Rooms | Tenants | Payments | Tenant Dashboard |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| AppHeader | ✓ | ✓ | ✓ | | | | | |
| NavigationSidebar | | | | ✓ | ✓ | ✓ | ✓ | ✓ |
| FormField | ✓ | ✓ | | | ✓ | ✓ | | |
| SearchBar | | ✓ | ✓ | | ✓ | ✓ | ✓ | |
| RoomCard | | ✓ | ✓ | | ✓ | | | |
| SummaryCard | | | | ✓ | | | | ✓ |
| DataTable | | | | ✓ | ✓ | ✓ | ✓ | ✓ |
| StatusBadge | | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| EmptyState | | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| LoadingState | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| ErrorState | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |

## 5. UI State Requirements

| State | Trigger | Example Message |
|---|---|---|
| Empty Rooms | No rooms are available or match the search. | No available rooms found. |
| Empty Tenants | No tenant records exist or match a filter. | No tenant records found. |
| Empty Payments | No payment records exist. | No payment records available. |
| Loading Rooms | Room data is being retrieved. | Loading room information... |
| Loading Tenants | Tenant data is being retrieved. | Loading tenant records... |
| Loading Payments | Payment data is being retrieved. | Loading payment records... |
| Login Error | Sign-in cannot be completed. | Unable to sign in. Check your details and try again. |
| Registration Error | The registration form is invalid or submission fails. | Registration could not be completed. Please try again. |
| Data Error | A list cannot be retrieved. | Unable to load records. Please try again. |
| Success | A valid action finishes successfully. | Changes saved successfully. |

For Week 6, make these states demonstrable with static placeholder data or local conditions. Week 7 can connect them to actual backend data and requests.

## 6. Suggested Component Organization

The following is an **illustrative** structure; use the actual framework and file conventions of the existing repository.

```text
src/
  components/
    layout/
      AppHeader
      NavigationSidebar
      TopNavigation
      PageTitle
    forms/
      FormField
      SelectField
      PrimaryButton
      SecondaryButton
    rooms/
      RoomCard
      RoomSelection
    shared/
      SearchBar
      FilterControls
      SummaryCard
      DataTable
      StatusBadge
      ActionMenu
      DetailCard
      ConfirmationDialog
      NotificationMessage
      EmptyState
      LoadingState
      ErrorState
      Pagination
      AccountMenu
    payments/
      PaymentSummary

docs/
  components.md
  ai-notes/
    week-06.md
```

If the project uses Laravel Blade, component files may instead belong in `resources/views/components/`. If it uses React, place the actual source files according to the project's existing `src` or `app` structure. Do not reorganize working code solely to match this example.

## 7. Team Task Distribution

| Team Member | Assigned Screens/Components |
|---|---|
| Auditor, Jan Marinelle | Login, AppHeader, FormField, PrimaryButton |
| Castro, Kaye | Registration, RoomSelection, SelectField |
| Lumod, Britney Jae | Room Catalog, RoomCard, SearchBar, Room Management |
| Guina, Mark Daryl | Admin Dashboard, Tenant Management, DataTable, StatusBadge |
| All Members | Tenant Dashboard, Payment Management, EmptyState, LoadingState, ErrorState |

All members should review common styles, accessibility, and navigation together. Reassign work to match the approved team board.

## 8. Accessibility and Design Guidelines

- Use semantic HTML elements such as `header`, `nav`, `main`, `section`, `form`, `label`, `input`, `button`, and `table`.
- Associate each input with a visible label.
- Use consistent spacing, text sizes, colors, and button styles.
- Ensure buttons and links are keyboard accessible.
- Provide meaningful messages for empty, loading, and error states.
- Use sufficient text contrast and visible focus states.
- Avoid using color alone to communicate a status.
- Keep layouts responsive on desktop and mobile screens.
- Match the approved project wireframes and existing visual style.

