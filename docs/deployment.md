# Week 11 — Deployment Notes

## Project

Integrated Boarding House Management System

## Deployment Overview

The application was prepared for production by fixing priority bugs, cleaning up technical debt, configuring environment variables, deploying the application, and performing smoke tests on the live version.

---

# Task 1 — Squash P0/P1 Bugs

The highest-priority bugs identified during Week 10 were reviewed and fixed.

## Bug 1 — Validation Errors

**Priority:** P1

**Problem:**  
Invalid form data was not always handled clearly.

**Fix:**  
Added validation and displayed clear field-level error messages.

**Test:**  
Submitted invalid data and confirmed that validation errors appeared.

**Status:** Fixed

---

## Bug 2 — Missing Record / 404

**Priority:** P1

**Problem:**  
Opening a record that does not exist could result in an unclear or blank page.

**Fix:**  
Added a clear not-found state.

**Test:**  
Requested a non-existent record and confirmed that the application displayed a not-found message.

**Message:**

> Record not found. The requested record may have been deleted or does not exist.

**Status:** Fixed

---

## Bug 3 — Server / Network Error

**Priority:** P1

**Problem:**  
Failed requests did not always provide useful feedback.

**Fix:**  
Added a user-friendly error message and Retry option where appropriate.

**Test:**  
Simulated a failed request and confirmed that the error state appeared.

**Message:**

> Something went wrong. Please try again.

**Status:** Fixed

---

## Bug 4 — Missing Loading Feedback

**Priority:** P1

**Problem:**  
Some asynchronous actions did not clearly show that they were processing.

**Fix:**  
Added loading states to asynchronous actions.

**Examples:**

- Saving...
- Deleting...
- Loading...
- Signing in...

Controls are disabled while the operation is processing.

**Status:** Fixed

---

## Bug Verification

Each P0/P1 bug was reproduced before the fix and tested again after the fix.

The changes were reviewed before merging.

---

# Task 2 — Pay Down Technical Debt

The application was reviewed for unnecessary duplication and inconsistent code.

## Technical Debt Item 1 — Repeated Feedback Logic

**Problem:**  
Loading, success, and error feedback was repeated across different parts of the application.

**Improvement:**  
Reused common feedback patterns so loading, success, and error states behave consistently.

**Status:** Completed

---

## Technical Debt Item 2 — Repeated Error Handling

**Problem:**  
Different pages handled API errors differently.

**Improvement:**  
Standardized error handling for:

- 422 validation errors
- 404 not found
- 500 server errors
- Network failures

**Result:**  
Users receive consistent and understandable error messages.

**Status:** Completed

---

# Task 3 — Set Up Environments & Configuration

Production configuration is stored using environment variables instead of hard-coded values.

## Environment Variables

The application uses environment variables for production configuration, including:

```text
DATABASE_URL
NEXT_PUBLIC_BASE_URL
NEXT_PUBLIC_API_URL


Security Checks

* No production secrets are committed to the repository.
* Secret .env files are excluded from version control.
* Required environment variables are documented without exposing real credentials.
* Production debug mode is disabled.
* Environment-specific configuration is separated from application code.

⸻

Task 4 — Deploy to a Live Host

The application was prepared and deployed to a live hosting environment.

Deployment Steps

1. Reviewed the application and fixed priority bugs.
2. Verified that tests pass.
3. Configured production environment variables.
4. Confirmed that no secrets were committed.
5. Built the production application.
6. Ran database migrations.
7. Deployed the application.
8. Opened the live application.
9. Performed smoke tests.

Live URL

https://integrated-boarding-house-managemen.vercel.app/login

Database

Production database migrations were executed before testing the live application.

Deployment Status

Deployed

⸻

Task 5 — Smoke-Test the Live App

The live application was tested using the public URL:

https://integrated-boarding-house-managemen.vercel.app/login

Happy Path

Login

The login page was opened and the authentication flow was tested.

Result: PASS

Create

A valid record was created through the application.

Result: PASS

View

Existing records were opened and displayed.

Result: PASS

Edit

An existing record was modified and saved.

Result: PASS

Delete

An existing record was deleted after confirmation.

Result: PASS

⸻

Failure Path

Invalid Data

Invalid or incomplete data was submitted.

Expected: Validation errors are displayed clearly.

Result: PASS

Non-Existent Record

A record that does not exist was requested.

Expected: A clear not-found state is displayed.

Result: PASS

Server / Network Failure

A failed request was simulated.

Expected:

* Loading state appears.
* Error message appears.
* Retry is available where appropriate.
* Application does not freeze.

Result: PASS

⸻

End-of-Lab Checklist

* [x]	P0/P1 bugs reviewed
* [x]	P0/P1 bugs fixed
* [x]	Bug fixes tested
* [x]	Changes reviewed before merging
* [x]	Technical debt items addressed
* [x]	Production environment variables configured
* [x]	No production secrets committed to the repository
* [x]	Production debug mode disabled
* [x]	Application deployed
* [x]	Database migrations completed
* [x]	Live URL recorded
* [x]	Login smoke test completed
* [x]	Create smoke test completed
* [x]	View smoke test completed
* [x]	Edit smoke test completed
* [x]	Delete smoke test completed
* [x]	Invalid-data failure path tested
* [x]	Non-existent-record failure path tested
* [x]	Server/network failure path tested

⸻

Deployment Result

The Integrated Boarding House Management System was prepared and deployed to a live hosting environment.

The highest-priority bugs were addressed, technical debt was reduced, production configuration was separated from source code, and database migrations were completed.

The live application was smoke-tested using both successful and failure scenarios.
