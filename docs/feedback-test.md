# Week 8 — Feedback Tests

## Purpose

This document records the failure-path tests performed for the Integrated Boarding House Management System.

## Test 1 — Invalid Data / 422

### Steps

1. Open a form that creates or updates a record.
2. Enter invalid or incomplete information.
3. Submit the form.
4. Observe the response.

### Expected Result

- The submit button shows a loading state while the request is processing.
- The request returns a validation error.
- The application does not show a blank screen.
- Validation errors are displayed beside the appropriate fields.
- The user can correct the information and submit again.

### Result

PASS

---

## Test 2 — Record Does Not Exist / 404

### Steps

1. Request a record that does not exist.
2. Wait for the request to finish.
3. Observe the page.

### Expected Result

- A loading state appears first.
- The application displays a clear not-found state.
- The user is not shown a blank page.
- The message explains that the requested record could not be found.

### Result

PASS

---

## Test 3 — Server Error / 500

### Steps

1. Simulate a failed server request.
2. Submit the affected action.
3. Observe the application.

### Expected Result

- The application displays a loading state.
- The application handles the server failure.
- A human-readable error message is displayed.
- A Retry action is available where appropriate.
- The application does not freeze.

### Result

PASS

---

## Test 4 — Slow Request

### Steps

1. Simulate a slow network request.
2. Start an asynchronous action.
3. Observe the interface while waiting.

### Expected Result

- A spinner, skeleton, or other loading indicator is visible.
- The user knows that the request is still processing.
- Relevant controls remain disabled while processing.
- The interface remains responsive.

### Result

PASS

---

## Test 5 — Delete Confirmation

### Steps

1. Select a record.
2. Click Delete.
3. Observe the confirmation dialog.
4. Cancel the operation.
5. Repeat and confirm the deletion.

### Expected Result

- A confirmation step appears before deletion.
- Cancel leaves the record unchanged.
- Confirm removes the record.
- A success message is displayed after deletion.

### Result

PASS

---

## Test 6 — Successful Create

### Steps

1. Enter valid information.
2. Submit the form.
3. Wait for the request to finish.

### Expected Result

- Loading feedback is displayed.
- The new record appears.
- A success message/toast is displayed.
- The form returns to its normal state.

### Result

PASS

---

## Test 7 — Successful Update

### Steps

1. Open an existing record.
2. Modify its information.
3. Save the changes.

### Expected Result

- Loading feedback is displayed.
- The updated information is displayed.
- A success message is displayed.
- The save control becomes available again.

### Result

PASS

---

## Summary

All major failure paths were tested:

- 422 validation error
- 404 not found
- 500 server error
- Network/slow request
- Delete confirmation
- Successful create
- Successful update

The application provides visible feedback instead of leaving the user on a blank or frozen screen.
