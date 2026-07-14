# BIT3208 – Student Management System (Continuous Project, Weeks 9–14)

A PHP + MySQL web application built on **XAMPP**, implementing the Weeks 9–14
course concepts (taught in the notes using Java Servlets / ASP.NET) in the
PHP environment, while incorporating Weeks 6–8 (CRUD, authentication,
responsive design).

## Technologies
- XAMPP (Apache + MySQL + PHP)
- phpMyAdmin for database administration
- HTML5, CSS3 (mobile-first responsive), JavaScript
- Git & GitHub for version control

## Setup (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\student-management-system`
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin` → **Import** → choose `studentdb.sql` → Go.
4. Visit `http://localhost/student-management-system/register.php` and create your account.
5. Make yourself admin — in phpMyAdmin run:
   `UPDATE users SET role='admin' WHERE username='yourusername';`
6. Log in at `http://localhost/student-management-system/login.php`

### LDAP test accounts (Week 13, simulated directory)
| UID | Password | Role |
|-----|----------|------|
| admin1 | Admin@2026 | admin |
| stud1 | Student@2026 | student |

## Feature ↔ Week mapping
| Week | Concept in course notes | Implementation here |
|------|------------------------|---------------------|
| 9 | Servlet lifecycle, HttpSession, cookies | `login.php`, `dashboard.php`, `logout.php`, `auth_check.php` — $_SESSION, session ID + login time display, theme cookie, Remember-Me cookie |
| 10 | Dynamic HTML + JDBC | `config/db.php` (mysqli), `add_student.php` (INSERT), `students.php` (dynamic table) |
| 11 | Full CRUD + search | `edit_student.php`, `delete_student.php`, search in `students.php`, prepared statements everywhere |
| 12 | ASP.NET object model, controls, web forms | `week12_forms.php` — DropDownList/GridView equivalents, control structures, Java/PHP/ASP.NET comparison |
| 13 | COM/CORBA/RMI/LDAP/search engines | `ldap_directory.php` + `ldap_login.php` (simulated LDAP bind), role-based access, student search |
| 14 | Testing, publishing, maintenance | `TESTING.md`, input validation, XSS/SQLi protection, GitHub deployment |
| 6–8 (incorporated) | CRUD, authentication, responsive design | CRUD module, password_hash/verify, `assets/style.css` mobile-first with 768px/1024px breakpoints |

## Security features (Week 14 checklist)
- Passwords hashed with `password_hash()` / verified with `password_verify()`
- All SQL uses prepared statements (`mysqli_prepare` + `bind_param`)
- Output escaped with `htmlspecialchars()` (XSS protection)
- `session_regenerate_id()` on login (session fixation protection)
- Protected pages redirect unauthenticated users
- Role-based access control (admin vs student)
- Server-side input validation on every form
