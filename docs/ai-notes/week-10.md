

## Manual QA & Bug Hunting

Week 10 focused on manual quality assurance, test coverage, adversarial testing, and bug triage.

## Testing Performed

The application was tested across:

- Login
- Create
- Read
- Update
- Delete
- Search
- Logout
- Validation
- Loading states
- Error states
- Empty states
- Permission scenarios

## Adversarial Testing

The following unusual scenarios were tested:

- Very large numbers
- Very long text
- Emoji
- Special characters
- HTML/script-like input
- Double-clicking submit buttons
- Direct access to non-existent records
- Accessing deleted records
- Slow network
- Network failure
- Empty form submission

## QA Results

The application was checked for:

- Correct behavior
- Input validation
- Error handling
- Loading feedback
- Duplicate submissions
- Empty states
- Not-found states
- Permission handling
- Security-related input handling

## Bug Triage

Bugs are categorized according to severity:

### P0 — Critical

A complete application failure, data loss, or critical security issue.

### P1 — Major

A major feature is broken or the application cannot complete an important workflow.

### P2 — Minor

A small issue that does not prevent normal use.

## Review Conclusion

The manual QA pass was completed using a test matrix covering normal, boundary, invalid, empty, and permission scenarios.

Adversarial testing was also performed to identify unexpected behavior and weaknesses.

Any discovered bugs should be recorded as issues with:

- Bug title
- Reproduction steps
- Expected result
- Actual result
- Severity
- Evidence
- Suggested fix

## AI Assistance

AI assistance was used for organizing test scenarios, identifying possible edge cases, and reviewing the test matrix.

All test results were manually checked before being recorded.

AI suggestions were treated as review assistance and not as proof that a test passed.
