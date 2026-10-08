# Week 8 — Feedback Matrix

## Purpose

This document defines the loading, success, and error feedback states for every major action in the Integrated Boarding House Management System.

| Action | Loading State | Success State | Error State |
|---|---|---|---|
| Login | Button shows "Signing in..." and is disabled | User is redirected to the dashboard and success feedback is shown | Invalid credentials message is displayed |
| Create record | Button shows "Saving..." and is disabled | New record appears and success toast is shown | 422 validation errors are shown beside the relevant fields |
| View/List records | Skeleton or spinner is displayed | Records are rendered | Network error message with Retry button |
| View single record | Loading spinner/skeleton | Record details are displayed | 404 "Record not found" state |
| Update record | Button shows "Saving..." and is disabled | Updated information appears and success toast is shown | Validation error is shown beside the relevant field |
| Delete record | Delete button is disabled and shows "Deleting..." | Record is removed and success toast is shown | General error message with Retry option |
| Search/Filter | Loading indicator is displayed | Matching records are displayed | Error message explains that results could not be loaded |
| Logout | Button shows "Signing out..." | User is returned to the login page | User-friendly logout error message |

## Error Categories

### 422 — Validation Error

The application must show field-level errors instead of exposing the raw HTTP status code.

Example:

> Quantity must be at least 1.

### 404 — Not Found

The application must show a clear not-found state.

Example:

> Record not found. The record may have been deleted or does not exist.

### 500 — Server Error

The application must show a helpful message.

Example:

> Something went wrong while processing your request. Please try again.

### Network Error

The application must tell the user that the request could not be completed.

Example:

> We couldn't connect to the server. Check your connection and try again.

## General Requirements

- Every asynchronous action has a loading state.
- Every successful action provides visible success feedback.
- Every failure provides visible and understandable feedback.
- Controls are disabled while an operation is in progress.
- Users are not shown raw stack traces or technical error messages.
- Retry is provided when retrying the operation is appropriate.
- Destructive actions such as Delete require confirmation.
- Feedback components should be consistent throughout the application.
