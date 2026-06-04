# Week 4 - Server-Side PHP, Authentication and Sessions

## What was done this week
- Built PHP login system using POST method form handling
- Implemented password hashing using password_hash(PASSWORD_BCRYPT)
- Implemented password verification using password_verify()
- Created PHP session management using $_SESSION
- Built role-based access control - admin, tourist, guide roles
- Created user dashboard showing personalised booking data
- Implemented logout using session_destroy()
- Applied prepared statements using bind_param() for SQL injection prevention

## Files added this week
- logout.php - session destroy and redirect
- pages/dashboard.php - user dashboard with session protection

## Technologies used
- PHP 8 - form processing, sessions, cookies
- bcrypt password hashing
- $_SESSION for authentication state
- MySQL prepared statements