# Binding Test Report


**Test focus:** Create and Update form binding, async lifecycle, and end-to-end data persistence.

## 1. Testing Scope

This report follows the Week 7 laboratory handout. It uses the publicly accessible Login and Tenant Registration pages of the deployed system as the starting point. Public-page inspection confirmed the forms. The project team additionally confirmed testing tenant registration and saving the submitted information to the database. Other scenarios and HTTP request details have not been independently verified.

## 2. Publicly Verified UI

| ID | Test/Inspection | Expected Result | Observed Result | Status |
|---|---|---|---|---|
| UI-01 | Open system homepage | Redirect to or show login screen | Redirects to `/login`; sign-in page visible | PASS (page inspection) |
| UI-02 | Inspect Login form | Email and password fields and Sign In control | All present | PASS (page inspection) |
| UI-03 | Open tenant registration | Registration form displayed | `/register` shows tenant registration | PASS (page inspection) |
| UI-04 | Inspect Create form fields | Required tenant fields and password confirmation | Full name, email, contact, home address, password, confirm password present; optional gender and date of birth | PASS (page inspection) |
| UI-05 | Inspect preferred room control | Can choose room or decide later | Room options and Decide Later shown | PASS (page inspection) |
| UI-06 | Confirm registration/database write | New tenant information stored in database | Project team confirms tenant data was saved | PASS (team-confirmed) |

## 3. Create Form Binding Tests

| ID | Test Steps | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| CR-01 | Fill required fields with valid data; click Register Account | POST request sent without full-page reload | Not performed | NOT TESTED |
| CR-02 | While registration request is pending | Button disabled; loading indicator visible; duplicate submission prevented | Not performed | NOT TESTED |
| CR-03 | Submit valid new tenant | New registration saved in database | Team confirms successful registration and database saving; approval state not verified | PASS (team-confirmed for saving) |
| CR-04 | Submit missing required values | Field-level validation messages; user input preserved | Not performed | NOT TESTED |
| CR-05 | Submit invalid email/contact or mismatched passwords | Clear field-level error messages (422 where applicable) | Not performed | NOT TESTED |
| CR-06 | Refresh/reopen after successful registration | Tenant record continues to exist | Database save team-confirmed; refresh/reopen not confirmed | NOT TESTED |

## 4. Update Form Binding Tests

**Prerequisite:** Sign in with an authorized test account and navigate to an editable tenant, room, or profile record.

| ID | Test Steps | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| UP-01 | Open Edit form | Existing database values pre-filled | Login required | NOT TESTED |
| UP-02 | Change one editable field and save | PUT/PATCH sent to matching controller | Login required | NOT TESTED |
| UP-03 | Reload record | Updated data still displayed | Login required | NOT TESTED |
| UP-04 | Submit invalid edited values | Field-level validation errors shown; no unwanted update | Login required | NOT TESTED |

## 5. Async Lifecycle/Error Handling Tests

| ID | Scenario | Expected UI behavior | Actual Result | Status |
|---|---|---|---|---|
| AS-01 | Pending create/update request | Spinner/loading status and disabled submit button | Not observed | NOT TESTED |
| AS-02 | Successful registration | Data saved and UI updates | Database save team-confirmed; response code and UI feedback not documented | PARTIAL |
| AS-03 | Validation failure (422) | Field-specific errors from server displayed | Not observed | NOT TESTED |
| AS-04 | Server error (500) | Friendly general failure message; app remains functional | Not observed | NOT TESTED |
| AS-05 | Network request failure | Visible failure message and ability to retry | Not observed | NOT TESTED |

## 6. End-to-End Scenarios

- [x] Register a tenant and confirm database save (project team confirmation).
- [ ] Confirm saved record after reload and in Admin panel.
- [ ] Edit an existing record; verify updates persist after reload.
- [ ] Submit intentionally invalid data; verify field-level errors appear.
- [ ] Simulate network failure or server error; verify visible message and recovery.
- [ ] Inspect browser DevTools > Network to identify endpoint, HTTP method, response code, and response body.

## 7. How to Record Evidence

1. Use non-sensitive test data and a dedicated test account.
2. Open browser DevTools (F12) and select the Network tab.
3. Submit a form and capture request method, URL path, status, and visible UI feedback.
4. For Create, verify the record in the application after refresh and in an authorized admin view.
5. For Update, record the original value, edited value, and persisted value after refresh.
6. Attach screenshots for success, loading, invalid/422, and error cases. Avoid exposing real passwords, credentials, session cookies, or personal data.
7. Change each NOT TESTED status only after the associated scenario is actually executed.

## 8. Conclusion

The deployed Integrated Boarding House Management System provides a reachable login interface and a public Tenant Registration form with required fields and room preferences. The project team confirms tenant registration saves submitted data to the database. Accordingly, the Create/database-saving scenario is marked **PASS (team-confirmed)**. The specific HTTP POST request, edit form PUT/PATCH behavior, persistence after reload, 422 field validation, loading UI, and 500/network error handling remain unverified and require additional test evidence.
