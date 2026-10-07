# Integrated Boarding House Management System

## Database Schema

---

## 1. Tenant

| Field Name | Data Type | Key | Description |
|---|---|---|---|
| `id` | BIGINT | PK | Unique identifier |
| `full_name` | VARCHAR(255) | — | Full name of tenant |
| `contact_number` | VARCHAR(20) | — | Contact number |
| `address` | TEXT | — | Tenant address |
| `room_id` | BIGINT | FK | Assigned room |
| `move_in_date` | DATE | — | Date of move-in |
| `status` | VARCHAR(50) | — | Tenant status |
| `created_at` | TIMESTAMP | — | Date created |
| `updated_at` | TIMESTAMP | — | Date updated |

---

## 2. Rooms

| Field Name | Data Type | Key | Description |
|---|---|---|---|
| `id` | BIGINT | PK | Unique identifier |
| `room_number` | VARCHAR(50) | — | Room number |
| `room_type` | VARCHAR(100) | — | Type of room |
| `capacity` | INT | — | Maximum occupants |
| `monthly_rent` | DECIMAL(10,2) | — | Monthly rental rate |
| `status` | VARCHAR(50) | — | Room status |
| `created_at` | TIMESTAMP | — | Date created |
| `updated_at` | TIMESTAMP | — | Date updated |

---

## 3. Payment

| Field Name | Data Type | Key | Description |
|---|---|---|---|
| `id` | BIGINT | PK | Unique identifier |
| `tenant_id` | BIGINT | FK | Tenant who made the payment |
| `amount` | DECIMAL(10,2) | — | Amount paid |
| `payment_date` | DATE | — | Date of payment |
| `payment_method` | VARCHAR(50) | — | Payment method |
| `reference_number` | VARCHAR(100) | — | Payment reference |
| `payment_status` | VARCHAR(50) | — | Payment status |
| `created_at` | TIMESTAMP | — | Date created |
| `updated_at` | TIMESTAMP | — | Date updated |

---

## 4. Maintenance Request

| Field Name | Data Type | Key | Description |
|---|---|---|---|
| `id` | BIGINT | PK | Unique identifier |
| `tenant_id` | BIGINT | FK | Tenant who submitted the request |
| `room_id` | BIGINT | FK | Affected room |
| `title` | VARCHAR(255) | — | Request title |
| `description` | TEXT | — | Description of the issue |
| `priority` | VARCHAR(50) | — | Priority level |
| `status` | VARCHAR(50) | — | Request status |
| `created_at` | TIMESTAMP | — | Date created |
| `updated_at` | TIMESTAMP | — | Date updated |

---

## Relationships

- `rooms.id` → `tenants.room_id`
- `tenants.id` → `payments.tenant_id`
- `tenants.id` → `maintenance_requests.tenant_id`
- `rooms.id` → `maintenance_requests.room_id`
