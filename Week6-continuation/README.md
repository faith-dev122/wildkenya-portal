# Week 6 Continuation - Complete Authentication Module

## What was done this week

This continuation builds on the original Week 6 CRUD work by confirming
and documenting the complete authentication module already running inside
the main WildKenya project. Each of the seven core requirements below was
tested directly on the live system and verified to be working correctly.

## Checklist Coverage

### Registration System

Tourists and Safari Guides can create an account through the registration
form, which collects name, email, phone, account type, and password.
Server-side validation checks that all required fields are filled, the
email format is valid, the password is at least 8 characters, and the two
password fields match before any database write happens.

### Secure Login

The login form authenticates a user by looking up their email in the
database and verifying the submitted password against the stored hash.
Incorrect credentials return a generic error message without revealing
whether the email or the password was wrong, which prevents account
enumeration.

### Password Hashing

No password is ever stored in plain text. Registration uses
password\_hash() with the bcrypt algorithm to convert the password into an
irreversible hash before saving it. Login uses password\_verify() to check
the submitted password against that hash without ever decrypting it.

### Session Management

A successful login creates a PHP session using $\_SESSION to store the
user's ID, name, email, and role. This session persists as the user
navigates between pages, allowing the system to recognise who is logged
in without asking them to re-authenticate on every page.

### Protected Dashboard

The dashboard page checks for an active session at the very top of the
file. If no session exists, the user is redirected immediately to the
login page using header() and exit(), preventing any dashboard content
from being shown to an unauthenticated visitor.

### Logout Functionality

The logout script clears all session data using session\_destroy() and
redirects the user back to the login page, fully ending their
authenticated state.

### GitHub Documentation

All authentication code, including the registration, login, dashboard,
and logout files, is version controlled and pushed to the wildkenya-portal
GitHub repository, with commit messages describing each stage of
development.

## Files Covered

* pages/register.php
* pages/login.php
* pages/dashboard.php
* logout.php
* includes/header.php (session\_start)

## Technologies Used

* PHP sessions
* bcrypt password hashing
* Prepared statements with bind\_param()
* Server-side and client-side input validation

## Database

* Week6db.sql included in this folder (export taken after re-testing
the authentication module)

