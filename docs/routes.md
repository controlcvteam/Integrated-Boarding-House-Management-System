# Integrated Boarding House Management System (IBHMS)

An all-in-one web-based platform that streamlines boarding house operations by centralizing tenant management, room tracking, payment processing, and maintenance request management.

---

## Week 3 — Routing Skeleton

This document contains the routing skeleton for the Integrated Boarding House Management System. It defines the RESTful routes and stub handlers for all CRUD operations identified in Week 2.

---

## Tenant Routes

| Method | Path | Handler | Example Request | Example Response |
|---|---|---|---|---|
| GET | `/api/tenants` | `listTenants` | `GET /api/tenants` | `{"message":"listTenants stub"}` |
| GET | `/api/tenants/:id` | `showTenant` | `GET /api/tenants/25` | `{"message":"showTenant stub","id":"25"}` |
| POST | `/api/tenants` | `createTenant` | `POST /api/tenants` | `{"message":"createTenant stub"}` |
| PUT | `/api/tenants/:id` | `updateTenant` | `PUT /api/tenants/25` | `{"message":"updateTenant stub","id":"25"}` |
| DELETE | `/api/tenants/:id` | `deleteTenant` | `DELETE /api/tenants/25` | `{"message":"deleteTenant stub","id":"25"}` |

---

## Room Routes

| Method | Path | Handler | Example Request | Example Response |
|---|---|---|---|---|
| GET | /api/rooms | listRooms | GET /api/rooms | {"message":"listRooms stub"} |
| GET | /api/rooms/:id | showRoom | GET /api/rooms/1 | {"message":"showRoom stub","id":"1"} |
| POST | /api/rooms | createRoom | POST /api/rooms | {"message":"createRoom stub"} |
| PUT | /api/rooms/:id | updateRoom | PUT /api/rooms/1 | {"message":"updateRoom stub","id":"1"} |
| DELETE | /api/rooms/:id | deleteRoom | DELETE /api/rooms/1 | {"message":"deleteRoom stub","id":"1"} |

---

## Payment Routes

| Method | Path | Handler | Example Request | Example Response |
|---|---|---|---|---|
| GET | /api/payments | listPayments | GET /api/payments | {"message":"listPayments stub"} |
| GET | /api/payments/:id | showPayment | GET /api/payments/1 | {"message":"showPayment stub","id":"1"} |
| POST | /api/payments | createPayment | POST /api/payments | {"message":"createPayment stub"} |
| PUT | /api/payments/:id | updatePayment | PUT /api/payments/1 | {"message":"updatePayment stub","id":"1"} |
| DELETE | /api/payments/:id | deletePayment | DELETE /api/payments/1 | {"message":"deletePayment stub","id":"1"} |

---

## Maintenance Request Routes

| Method | Path | Handler | Example Request | Example Response |
|---|---|---|---|---|
| GET | `/api/maintenance-requests` | `listMaintenanceRequests` | `GET /api/maintenance-requests` | `{"message":"listMaintenanceRequests stub"}` |
| GET | `/api/maintenance-requests/:id` | `showMaintenanceRequest` | `GET /api/maintenance-requests/1` | `{"message":"showMaintenanceRequest stub","id":"1"}` |
| POST | `/api/maintenance-requests` | `createMaintenanceRequest` | `POST /api/maintenance-requests` | `{"message":"createMaintenanceRequest stub"}` |
| PUT | `/api/maintenance-requests/:id` | `updateMaintenanceRequest` | `PUT /api/maintenance-requests/1` | `{"message":"updateMaintenanceRequest stub","id":"1"}` |
| DELETE | `/api/maintenance-requests/:id` | `deleteMaintenanceRequest` | `DELETE /api/maintenance-requests/1` | `{"message":"deleteMaintenanceRequest stub","id":"1"}` |

---

## Routing Summary

| Resource | List | Show | Create | Update | Delete | Total |
|---|---|---|---|---|---|---:|
| Tenant | ✓ | ✓ | ✓ | ✓ | ✓ | 5 |
| Room | ✓ | ✓ | ✓ | ✓ | ✓ | 5 |
| Payment | ✓ | ✓ | ✓ | ✓ | ✓ | 5 |
| Maintenance Request | ✓ | ✓ | ✓ | ✓ | ✓ | 5 |
| **Total** | **4** | **4** | **4** | **4** | **4** | **20** |

---

## HTTP Status Codes

| Operation | Status Code | Meaning |
|---|---:|---|
| List | `200` | Request successful |
| Show | `200` | Request successful |
| Create | `201` | Resource created |
| Update | `200` | Update successful |
| Delete | `200` | Delete successful |

---

## Scope

This document covers **Week 3 routing skeleton only**.

