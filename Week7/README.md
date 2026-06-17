# Week 7 - Authentication Bonus Features

## What was done this week

* Added Remember Me functionality to the login system
* Built a User Profile Management page
* Added a Change Password feature with current password verification
* Updated logout to also clear the Remember Me cookie

## Files added or updated this week

* pages/login.php - added a Remember Me checkbox that sets a 30-day cookie
* pages/register.php - simplified registration flow
* pages/profile.php - new page for editing name, email, phone, and password
* includes/remember\_me.php - checks the Remember Me cookie and restores the session automatically
* logout.php - clears both the session and the Remember Me cookie

## How Remember Me works

When a user logs in with the Remember Me box checked, a cookie is created
containing their user ID and a verification token. The token is generated
using hash\_hmac with the user's password hash as the key, so the cookie
automatically becomes invalid if the password is ever changed. On future
visits, if no active session exists but the cookie is present and valid,
the user is logged back in automatically without re-entering their password.

## How Profile Management works

A logged-in user can view and update their name, email, and phone number.
A separate form allows changing the password, which requires the current
password to be verified with password\_verify() before the new password is
hashed with password\_hash() and saved.

## Technologies used

* PHP sessions and cookies
* hash\_hmac() for secure cookie tokens
* password\_hash() and password\_verify() for password security
* Prepared statements for all database updates

## Database

* Week7db.sql included in this folder (export taken after testing these features)

