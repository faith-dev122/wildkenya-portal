# Week 14 – Testing, Publishing & Maintenance Checklist

## Manual Testing Checklist (tick after verifying, screenshot the completed table)

| # | Feature | Test performed | Pass |
|---|---------|----------------|------|
| 1 | Registration | Register a new user; empty fields rejected; invalid email rejected; password mismatch rejected | ☐ |
| 2 | Password hashing | Open `users` table in phpMyAdmin — password column shows bcrypt hash, not plain text | ☐ |
| 3 | Login (DB) | Correct credentials → dashboard; wrong password → error message | ☐ |
| 4 | Login (LDAP) | `admin1/Admin@2026` and `stud1/Student@2026` bind successfully; wrong password rejected | ☐ |
| 5 | Session | Dashboard shows session ID and login time; refresh keeps you logged in | ☐ |
| 6 | Protected pages | Open `dashboard.php` in a private window without logging in → redirected to login | ☐ |
| 7 | Logout | Click Logout → session destroyed → dashboard redirects to login again | ☐ |
| 8 | Remember Me | Tick checkbox, close browser, reopen login page → username auto-filled | ☐ |
| 9 | Theme cookie | Switch to Dark, restart browser → still dark | ☐ |
| 10 | Add student | Valid record saved; duplicate reg number rejected; success message shown | ☐ |
| 11 | View students | Table lists all records from MySQL dynamically | ☐ |
| 12 | Edit student | Form pre-filled; update reflected in list and in phpMyAdmin | ☐ |
| 13 | Delete student | Confirmation dialog shown; record removed after confirming | ☐ |
| 14 | Search | Search by name, reg no, and course each return correct rows | ☐ |
| 15 | No results | Searching gibberish shows the friendly "No records found" message | ☐ |
| 16 | Role-based access | Admin sees Edit/Delete buttons; student role does not | ☐ |
| 17 | SQL injection | Enter `' OR '1'='1` in login and search — no bypass (prepared statements) | ☐ |
| 18 | XSS | Add a student named `<script>alert(1)</script>` — displayed as text, no popup (htmlspecialchars) | ☐ |
| 19 | Responsive | Resize DevTools to 375px, 800px, 1200px — layout adapts (1/2/3 columns) | ☐ |
| 20 | Error handling | Stop MySQL in XAMPP, load a page — friendly connection error, no stack trace | ☐ |

## Publishing to GitHub

```bash
cd C:\xampp\htdocs\student-management-system
git init
git add .
git commit -m "Week 9: sessions, cookies, remember-me and theme preference"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/BIT3208-student-management-system.git
git push -u origin main
```

Commit at the END OF EVERY WEEK with a descriptive message, e.g.:

- `Week 10: MySQL connectivity, add student form, dynamic student list`
- `Week 11: full CRUD with prepared statements and search`
- `Week 12: web forms, control structures and framework comparison`
- `Week 13: simulated LDAP authentication and role-based access`
- `Week 14: testing, security checklist, documentation and final deployment`

## Database export (submit with the project)

phpMyAdmin → select `studentdb` → **Export** tab → Quick / SQL → **Go** → save as `studentdb.sql` and commit it to the repository.

## Maintenance plan

- Back up `studentdb` weekly via phpMyAdmin Export.
- Review Apache/PHP error logs in `C:\xampp\apache\logs`.
- Keep XAMPP (PHP/MySQL) updated.
- Re-run this checklist after every change (regression testing).
