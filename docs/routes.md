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
