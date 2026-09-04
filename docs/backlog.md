# Integrated Boarding House Management System Backlog

## Project

Integrated Boarding House Management System

---

# Tenant 

**By: Jan Marinelle Auditor**

## Create Tenant

**User Story** As an admin, I want to create a new tenant record so that I can register new boarders in the system.

**Acceptance Criteria**

* The Add Tenant form is accessible to users.
* All required fields must be completed and invalid information is rejected.
* The tenant record is saved successfully and appears in the tenant list.

---

## Read Tenant - List View

**User Story** As an admin, I want to view a list of tenant records so that I can monitor and manage all registered tenants.

**Acceptance Criteria**

* The tenant list displays all registered tenant records accurately.
* The search function allows the admin to find specific tenant records.
* The system displays an appropriate message when no tenant records are available.

---

## Read Tenant - Detail View

**User Story** As an admin, I want to view the complete details of a tenant record so that I can access all relevant tenant information when needed.

**Acceptance Criteria**

* The admin can view the complete details of a selected tenant record.
* All tenant information is displayed accurately and completely.
* The system displays an appropriate message if the selected tenant record cannot be found.

---

## Update Tenant

**User Story** As an admin, I want to update tenant information so that tenant records remain accurate and up to date.

**Acceptance Criteria**

* Existing tenant information can be edited by authorized users, and the updated details are properly displayed in the edit form.
* Updated tenant information is validated to ensure that all required fields are completed and correct before saving.
* Changes are saved successfully, and the updated tenant information is reflected in the tenant list.

---

## Delete Tenant

**User Story** As an admin, I want to delete a tenant record so that inactive or incorrect records can be removed.

**Acceptance Criteria**

* A confirmation message is displayed before deleting the selected tenant record to prevent accidental removal.
* The selected tenant record is successfully deleted from the system after confirmation.
* The deleted tenant no longer appears in the tenant list, and a success message is displayed after deletion.

---

# Room 

**By: Kaye Castro**

## Create Room

**User Story** As a boarding house administrator, I want to register a new room in the system so that available accommodations can be organized and assigned to tenants efficiently.

**Acceptance Criteria**

* The administrator can enter the room details, including room number, room type, capacity, rental fee, and availability status.
* The system validates all required fields before saving.
* The room information is stored successfully in the database.

---

## Read Room - List View

**User Story** As an admin, I want to view the list of rooms so that I can monitor and manage all room records.

**Acceptance Criteria**

* The administrator can access the Room Management page.
* The system displays a list of all registered rooms.
* The room list includes the room number, room type, capacity, rental fee, and availability status.

---

## Read Room - Detail View

**User Story** As an admin, I want to view the details of a specific room so that I can review its complete information before performing any action.

**Acceptance Criteria**

* The administrator can select a room from the room list.
* The system displays the complete room details, including the room number, room type, capacity, rental fee, and availability status.
* The displayed room information is accurate and up to date.

---

## Update Room

**User Story** As an admin, I want to update room information so that room records remain accurate and up to date.

**Acceptance Criteria**

* Existing room information can be edited by authorized users, and the updated details are properly displayed in the edit form.
* Updated room information is validated to ensure that all required fields are completed and correct before saving.
* Changes are saved successfully, and the updated room information is reflected in the Room Management list.

---

## Delete Room

**User Story** As an admin, I want to delete room information so that outdated or unused room records are removed from the system.

**Acceptance Criteria**

* The administrator can select a room and initiate the delete action from the Room Management page.
* The system requests confirmation before permanently deleting the selected room.
* The selected room is deleted successfully, and the Room Management list is updated to reflect the changes.

---

# Payment 

**By: Britney Lumod**

## Create Payment

**User Story** As an Admin, I want to record a tenant's payment so that rental transactions are properly documented and the tenant's balance is updated.

**Acceptance Criteria**

* The system requires all mandatory payment details before saving.
* The payment record is successfully stored in the database.
* The tenant's payment status and outstanding balance are automatically updated.

---

## Read Payment - Detail View

**User Story** As an Admin, I want to view the complete details of a payment transaction so that I can verify payment information.

**Acceptance Criteria**

* The system displays complete payment details for the selected record.
* All payment information is accurate and up to date.
* The selected payment record matches the displayed details.

---

## Read Payment - List View

**User Story** As an Admin, I want to view a list of all payment records so that I can easily monitor and search rental payments.

**Acceptance Criteria**

* The system displays all payment records in a table.
* The user can search, filter, and sort payment records.
* The payment list refreshes automatically after changes are made.

---

## Update Payment

**User Story** As an Admin, I want to edit an existing payment record so that I can correct incorrect payment information.

**Acceptance Criteria**

* The system allows editing of payment details.
* Updated information is validated before saving.
* The system confirms that the payment record has been updated successfully.

---

## Delete Payment

**User Story** As an Admin, I want to delete an incorrect or duplicate payment record so that only valid payment records remain in the system.

**Acceptance Criteria**

* The system asks for confirmation before deletion.
* The selected payment record is permanently removed after confirmation.
* The payment list is updated immediately after deletion.

---

# Maintenance Request 

**By: Mark Daryl Rey Guina**

## Create Maintenance Request

**User Story** As a tenant, I want to submit a maintenance request so that the administrator can resolve issues in my room or the boarding house.

**Acceptance Criteria**

* Tenant can create a maintenance request.
* Request is saved with a default Pending status.
* Administrator can update the status (Pending, In Progress, Resolved, Rejected).

---

## Read Maintenance Request - List View

**User Story** As a boarding house manager, I want to view a list of all maintenance requests so that I can monitor, track, and manage reported issues efficiently.

**Acceptance Criteria**

* Display a list of maintenance requests with key information (title, requester, priority, status, and date submitted).
* Allow users to view the details of a selected maintenance request.
* Show a message when there are no maintenance requests available.

---

## Read Maintenance Request - Detail View

**User Story** As a boarding house manager, I want to view the complete details of a maintenance request so that I can review the issue, its status, and other relevant information before taking action.

**Acceptance Criteria**

* Display complete maintenance request details (title, description, category, priority, status, requester, and date submitted).
* Show any attached images or supporting files, if available.
* Allow users to return to the maintenance request list from the detail view.

---

## Update Maintenance Request

**User Story** As an administrator, I want to update the status and details of a maintenance request so that tenants are informed of its progress.

**Acceptance Criteria**

* Administrator can update the request status (Pending, In Progress, Resolved, Rejected).
* Administrator can edit the request details and add remarks.
* Changes are saved and reflected when the tenant views the request.

---

## Delete Maintenance Request

**User Story** As an administrator, I want to delete a maintenance request so that invalid or duplicate requests can be removed from the system.

**Acceptance Criteria**

* Administrator can delete a maintenance request.
* The system asks for confirmation before deletion.
* The deleted request is removed from the maintenance request list.
