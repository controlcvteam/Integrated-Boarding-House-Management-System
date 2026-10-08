## Find the Flaw

### Finding 1 — Missing Input Validation

**What is wrong:**  
The form accepts invalid or incomplete data without properly validating the input before submitting it.

**Why it is a problem:**  
Invalid data can be sent to the server and may create incorrect records or cause request failures.

**Fix:**  
Add validation for required fields and display clear field-level error messages before submitting.

**Example:**
> Quantity must be at least 1.

**Severity:** Medium

**Status:** Fixed

---

### Finding 2 — Missing 404 Handling

**What is wrong:**  
The application assumes that the requested record always exists.

**Why it is a problem:**  
Requesting a record that does not exist can result in a blank or broken screen.

**Fix:**  
Handle the 404 response and display a clear message.

**Example:**
> Record not found. The requested record may have been deleted or does not exist.

**Severity:** Medium

**Status:** Fixed

---

### Finding 3 — Incorrect 500 Error Handling

**What is wrong:**  
Server errors are not handled with a useful message.

**Why it is a problem:**  
Users may not know what happened or what they should do next.

**Fix:**  
Handle server errors and provide a Retry option.

**Example:**
> Something went wrong. Please try again.

**Severity:** High

**Status:** Fixed

---

### Finding 4 — Missing Loading State

**What is wrong:**  
Some asynchronous actions do not show a loading indicator.

**Why it is a problem:**  
Users may think the application is frozen or submit the same request multiple times.

**Fix:**  
Show a loading state and disable the relevant control while the request is processing.

**Examples:**
- Saving...
- Deleting...
- Loading...
- Signing in...

**Severity:** Medium

**Status:** Fixed

---

### Finding 5 — No Retry After Failed Request

**What is wrong:**  
A failed request does not always provide a way to retry.

**Why it is a problem:**  
Users may have to refresh the page or repeat the entire process.

**Fix:**  
Add a Retry button when the operation can safely be repeated.

**Example:**
> We couldn't load the records. Please try again.

**Severity:** Medium

**Status:** Fixed

---

### Finding 6 — Technical Error Messages Shown to Users

**What is wrong:**  
Raw status codes or technical errors may be displayed directly to users.

**Bad example:**
`500 Internal Server Error`

**Fix:**  
Replace technical messages with clear, human-readable feedback.

**Better example:**
> Something went wrong while processing your request. Please try again.

**Severity:** Medium

**Status:** Fixed

---

### Finding 7 — Untrusted User Input

**What is wrong:**  
User-provided input should not be trusted without validation.

**Why it is a problem:**  
Unexpected or malicious input can cause incorrect behavior or security problems.

**Fix:**  
Validate and safely handle user input on both the client and server. Never rely only on client-side validation for security.

**Severity:** High

**Status:** Reviewed

---

## Summary

The AI-generated code was reviewed for:

- Correctness
- Readability
- Consistency
- Security
- Input validation
- Error handling
- Loading states
- 404 and 500 responses
- Edge cases
- Test coverage

The identified flaws were documented and fixes were applied or marked for review.

## Final Review

The main lesson from the review is that AI-generated code should not be assumed to be correct. Every change must be reviewed, tested, and checked for error paths and security issues before merging.
