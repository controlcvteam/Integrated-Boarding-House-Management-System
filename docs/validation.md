# Input Validation Matrix

## Tenants

### POST /api/tenants

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| full_name | required | string | 2-100 characters | plain text | - | - |
| email | required | string | max 255 characters | email format | - | - |
| contact_number | required | string | max 15 characters | phone number | - | - |
| password | required | string | minimum 8 characters | alphanumeric | - | - |
| room_id | required | integer | >= 1 | - | - | must exist in rooms |

### PUT /api/tenants/{id}

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| full_name | optional (if provided) | string | 2-100 characters | plain text | - | - |
| email | optional (if provided) | string | max 255 characters | email format | - | - |
| contact_number | optional/nullable | string | max 15 characters | phone number | - | - |
| room_id | optional (if provided) | integer | >= 1 | - | - | must exist in rooms |


## Rooms

### POST /api/rooms

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| room_number | required | string | 1-20 characters | plain text | - | must be unique |
| capacity | required | integer | >= 1 | numeric | - | - |
| monthly_rent | required | number | > 0 | decimal number | - | - |
| status | required | string | - | plain text | Available, Occupied | - |

### PUT /api/rooms/{id}

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| room_number | optional (if provided) | string | 1-20 characters | plain text | - | must be unique |
| capacity | optional (if provided) | integer | >= 1 | numeric | - | - |
| monthly_rent | optional (if provided) | number | > 0 | decimal number | - | - |
| status | optional (if provided) | string | - | plain text | Available, Occupied | - |


## Payments

### POST /api/payments

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| tenant_id | required | integer | >= 1 | - | - | must exist in tenants |
| amount | required | number | > 0 | decimal number | - | - |
| payment_date | required | date | valid date | YYYY-MM-DD | - | - |
| payment_status | required | string | - | plain text | Paid, Pending | - |

### PUT /api/payments/{id}

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| amount | optional (if provided) | number | > 0 | decimal number | - | - |
| payment_date | optional (if provided) | date | valid date | YYYY-MM-DD | - | - |
| payment_status | optional (if provided) | string | - | plain text | Paid, Pending | - |


## Maintenance Requests

### POST /api/maintenance-requests

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| tenant_id | required | integer | >= 1 | - | - | must exist in tenants |
| title | required | string | 3-100 characters | plain text | - | - |
| description | required | string | minimum 10 characters | plain text | - | - |
| status | required | string | - | plain text | Pending, In Progress, Completed | - |

### PUT /api/maintenance-requests/{id}

| Field | Presence | Type | Length/Range | Format | Allowed Values | Referential |
|---|---|---|---|---|---|---|
| title | optional (if provided) | string | 3-100 characters | plain text | - | - |
| description | optional (if provided) | string | minimum 10 characters | plain text | - | - |
| status | optional (if provided) | string | - | plain text | Pending, In Progress, Completed | - |




> Validation rules:
> - Required fields must not be empty.
> - Data must follow the defined type and format.
> - Referential fields must exist in the related database tables.
> - Duplicate records must be prevented where applicable.
> - Invalid input must return proper validation errors.
