
# Week 9 — AI Notes

## Reviewing Code

Week 9 focused on reviewing AI-generated code rather than simply generating new features.

The review concentrated on correctness, readability, consistency, security, and testing.

## Review Checklist

### Correctness

- Checked whether the code behaves as intended.
- Checked normal and edge-case behavior.
- Checked error paths.
- Checked API response handling.

### Readability

- Reviewed variable and function names.
- Checked that the structure was understandable.
- Looked for unnecessary duplication.

### Consistency

- Checked that the implementation follows existing application patterns.
- Checked response and error handling.
- Checked loading and success feedback.

### Security

- Checked input validation.
- Checked that user input is not blindly trusted.
- Checked for possible injection risks.
- Checked that sensitive information is not exposed in error messages.
- Checked that secrets are not committed to the repository.

### Tests

- Checked whether automated tests cover important behavior.
- Checked error paths and edge cases.
- Verified that changes should pass before merging.

## Bugs Found

The review identified several areas that required attention:

1. Missing or incomplete input validation.
2. Incomplete HTTP error handling.
3. Missing 404 handling.
4. Missing loading states.
5. Missing retry/recovery behavior.
6. Error messages that could expose technical information.
7. Security concerns around untrusted input.

## Improvements Made

The identified issues were addressed by:

- Adding input validation.
- Adding loading states.
- Handling 422 validation responses.
- Handling 404 responses.
- Handling 500/server errors.
- Adding user-friendly error messages.
- Adding Retry actions where appropriate.
- Reviewing user-controlled input.
- Improving consistency across components.

## Code Review Rule

No pull request should be merged without:

- Passing tests
- At least one approving review
- Addressing review comments
- Checking for security problems
- Confirming that the change does not introduce regressions

## AI Assistance

AI assistance was used for code review, identifying potential bugs, suggesting edge cases, improving error messages, and checking implementation consistency.

AI-generated suggestions were reviewed by a team member before being accepted.

AI output was not treated as automatically correct.

## Review Conclusion

The code was reviewed with particular attention to subtle bugs and security issues that can occur in AI-generated code.

The final implementation should be understandable, tested, secure, and consistent with the rest of the application.
