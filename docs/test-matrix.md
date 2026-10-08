# Week 10 — Manual QA Test Matrix

## Test Matrix

| Feature | Happy Path | Boundary | Invalid | Empty | Permissions | Result |
|---|---|---|---|---|---|---|
| Login | Valid email and password logs in | Minimum valid credentials | Wrong password | Empty email/password | Unauthorized user denied | PASS |
| Create Record | Valid record is created | Minimum valid values | Invalid field values | Required fields empty | Unauthorized user cannot create | PASS |
| View Records | Records load correctly | Large number of records | Invalid request | No records available | Unauthorized user denied | PASS |
| View Record | Existing record opens | Long record details | Non-existent record | Missing record ID | Unauthorized access denied | PASS |
| Update Record | Valid changes are saved | Minimum valid values | Invalid values | Required fields empty | Unauthorized user cannot update | PASS |
| Delete Record | Record is deleted after confirmation | Delete last record | Invalid/non-existent ID | Missing ID | Unauthorized user cannot delete | PASS |
| Search | Matching records appear | Very long search text | Invalid search input | Empty search | Restricted results remain protected | PASS |
| Logout | User is logged out | Session near expiration | Invalid session | No active session | Protected session is terminated | PASS |

## Detailed Test Cases

### TC-01 — Valid Login

**Steps:**
1. Open the login page.
2. Enter a valid email.
3. Enter the correct password.
4. Click Login.

**Expected:** User is successfully logged in and redirected to the dashboard.

**Result:** PASS

---

### TC-02 — Invalid Login

**Steps:**
1. Open the login page.
2. Enter an incorrect password.
3. Click Login.

**Expected:** Login fails and a helpful error message is displayed.

**Result:** PASS

---

### TC-03 — Empty Login Fields

**Steps:**
1. Leave the email and password empty.
2. Click Login.

**Expected:** Required-field validation messages appear.

**Result:** PASS

---

### TC-04 — Create Record

**Steps:**
1. Open the create form.
2. Enter valid information.
3. Submit the form.

**Expected:** Loading feedback appears, the record is created, and a success message is shown.

**Result:** PASS

---

### TC-05 — Create Invalid Record

**Steps:**
1. Open the create form.
2. Enter invalid information.
3. Submit the form.

**Expected:** Validation errors appear beside the incorrect fields and no invalid record is created.

**Result:** PASS

---

### TC-06 — Empty Required Fields

**Steps:**
1. Open a create/edit form.
2. Leave required fields empty.
3. Submit.

**Expected:** Required-field errors are displayed.

**Result:** PASS

---

### TC-07 — View Records

**Steps:**
1. Open the records/list page.
2. Wait for the data to load.

**Expected:** A loading state appears first, followed by the available records.

**Result:** PASS

---

### TC-08 — No Records

**Steps:**
1. Open a list with no available records.

**Expected:** A clear empty state is displayed instead of a blank or broken page.

**Result:** PASS

---

### TC-09 — Non-Existent Record

**Steps:**
1. Open a URL for a record that does not exist.

**Expected:** A clear "Record not found" message is displayed.

**Result:** PASS

---

### TC-10 — Update Record

**Steps:**
1. Open an existing record.
2. Edit the information.
3. Save the changes.

**Expected:** Loading feedback appears, the changes are saved, and a success message appears.

**Result:** PASS

---

### TC-11 — Delete Record

**Steps:**
1. Open an existing record.
2. Click Delete.
3. Confirm the deletion.

**Expected:** Confirmation appears before deletion. After confirmation, the record is removed and success feedback is displayed.

**Result:** PASS

---

### TC-12 — Cancel Delete

**Steps:**
1. Click Delete.
2. Select Cancel.

**Expected:** The record remains unchanged.

**Result:** PASS

---

## Adversarial Tests

### Weird Input

Tested:

- Very large numbers
- Very long text
- Emoji
- Special characters
- HTML-like input such as `<script>`

**Expected:** Application remains stable and does not execute unsafe input.

**Result:** PASS

### Double Submit

**Steps:**
1. Submit a form.
2. Quickly click the submit button multiple times.

**Expected:** The button becomes disabled while processing and duplicate records are not created.

**Result:** PASS

### Refresh During Request

**Steps:**
1. Start an asynchronous request.
2. Refresh or navigate away.

**Expected:** Application recovers without corrupted UI state.

**Result:** PASS

### Non-Existent URL

**Steps:**
1. Directly open a URL containing an invalid record ID.

**Expected:** A clear not-found state appears.

**Result:** PASS

### Deleted Record

**Steps:**
1. Open a record.
2. Delete the record.
3. Try to access it again.

**Expected:** The application displays a not-found state.

**Result:** PASS

### Network Failure

**Steps:**
1. Simulate a network failure.
2. Perform an operation requiring the server.

**Expected:** Loading ends with a helpful error message and Retry option.

**Result:** PASS

---

## QA Summary

Total major scenarios tested: 12

Adversarial scenarios tested: 6

Overall result: PASS

All major features were tested using happy-path, boundary, invalid, empty, and permission scenarios. Failure paths were also tested to ensure that the application provides useful feedback without freezing or displaying a blank screen.
