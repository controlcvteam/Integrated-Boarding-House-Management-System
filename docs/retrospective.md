# Week 12 — Final Retrospective

## Project

Integrated Boarding House Management System

---

# 1. What Went Well

Several parts of the project went well during development.

## Team Collaboration

The team divided the work into different tasks and worked together throughout the development process. Communication helped us coordinate development, testing, bug fixing, and deployment.

## Feature Development

The main features of the system were successfully implemented, including:

- User login
- Creating records
- Viewing records
- Updating records
- Deleting records
- Searching and filtering
- User logout

## Testing

The application was tested using both normal and failure scenarios.

We tested:

- Valid form submissions
- Invalid form submissions
- Missing records
- Server errors
- Network failures
- Loading states
- Delete confirmation
- Repeated submissions

These tests helped identify problems before deployment.

## Error Handling

The application was improved to provide clearer feedback to users.

Examples include:

- Validation messages for invalid input
- Not-found messages for missing records
- Loading indicators during requests
- User-friendly error messages
- Retry actions when appropriate
- Confirmation before deleting records

## Deployment

The application was successfully prepared for deployment and made available through a live URL.

Production configuration was separated from source code using environment variables.

---

# 2. What Did Not Go Well

The project also had several challenges.

## Initial Error Handling

Some errors were not handled clearly during the earlier stages of development.

For example, invalid input, missing records, and failed requests did not always provide useful feedback to the user.

These issues were identified during testing and improved before deployment.

## Repeated Code

Some parts of the application contained repeated loading and error-handling logic.

This made the code harder to maintain and could result in inconsistent behavior.

The code was reviewed and common patterns were reused where possible.

## Testing Took Time

Testing every feature and failure scenario required more time than expected.

Some bugs only became noticeable when unusual inputs or failed requests were tested.

This showed the importance of testing edge cases early instead of only testing the happy path.

---

# 3. What We Would Change Next Time

If we started the project again, we would make several improvements.

### 1. Plan the architecture earlier

We would define the structure of the application, API behavior, and reusable components earlier in the project.

### 2. Test earlier

Instead of waiting until the later stages, we would test each feature immediately after implementing it.

### 3. Standardize error handling

We would create common error-handling patterns earlier so that all pages provide consistent feedback.

### 4. Improve task distribution

We would divide tasks more clearly at the beginning and establish deadlines for each feature.

### 5. Document decisions

We would record important technical decisions while developing instead of documenting them mostly near the end.

---

# 4. What We Learned

This project helped us understand more than just how to build a web application.

We learned how to:

- Work with a team on a shared codebase
- Design and implement CRUD functionality
- Handle validation and errors
- Test normal and abnormal application behavior
- Identify and fix bugs
- Reduce technical debt
- Configure environment variables
- Deploy an application
- Perform smoke testing
- Review code before deployment
- Explain and defend our technical decisions

We also learned that a working application is not enough. A good application should also handle unexpected situations and provide clear feedback to its users.

---

# 5. AI Usage

AI was used as a development support tool throughout the project.

It helped with:

- Reviewing possible bugs
- Suggesting improvements to error handling
- Organizing testing scenarios
- Reviewing deployment requirements
- Preparing documentation
- Explaining technical concepts

AI suggestions were reviewed by the team and were not treated as a replacement for testing or developer judgment.

The final code and decisions remained the responsibility of the team.

---

# 6. Final Reflection

The project started as a collection of basic requirements and developed into a working web application through multiple stages of implementation, testing, debugging, and deployment.

The testing process was especially important because it revealed issues that were not obvious during normal use.

The project also showed us the importance of handling edge cases, validating user input, providing useful feedback, and testing before deployment.

Overall, the project improved our understanding of the complete software development process, from planning and coding to testing, deployment, and defending our own work.

---

# 7. Final Status

The Integrated Boarding House Management System was developed, tested, reviewed, and prepared for live demonstration.

The project includes:

- [x] Core application features
- [x] Validation
- [x] Error handling
- [x] Loading states
- [x] CRUD operations
- [x] Testing
- [x] Bug fixing
- [x] Production configuration
- [x] Deployment
- [x] Smoke testing
- [x] Final documentation

## Live Application

https://integrated-boarding-house-managemen.vercel.app/login

---

# Conclusion

This project gave the team practical experience in building, testing, reviewing, and deploying a web application.

The biggest lesson was that software development does not end when the features work. Testing, error handling, deployment, documentation, and the ability to explain our own code are all important parts of building a reliable application.
