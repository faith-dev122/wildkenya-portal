# Screenshot Guide – Weeks 9–14 Logbook Figures

Take each screenshot, then paste it under the matching figure heading in the logbook.

## WEEK 9 – Session Management and Cookies
1. **Fig 1** – Open `login.php`. Capture the form showing username, password and the *Remember Me for 30 days* checkbox.
2. **Fig 2** – Log in. Capture the dashboard showing the welcome message, **Session ID** and **Login Time** card.
3. **Fig 3** – Press F12 → Application tab → Cookies → localhost. Capture `remember_user`, `theme` and `PHPSESSID` cookies.
4. **Fig 4** – Click 🌙 Dark on the dashboard. Capture the dashboard in dark theme.
5. **Fig 5** – Close the browser completely, reopen `login.php`. Capture the username auto-filled (Remember Me working).
6. **Fig 6** – In a private/incognito window, go directly to `dashboard.php`. Capture the redirect to login with the "Please log in first" message.

## WEEK 10 – Database Connectivity and Dynamic HTML
1. **Fig 1** – phpMyAdmin showing the `studentdb` database with the `users` and `students` tables.
2. **Fig 2** – `config/db.php` open in your editor (the mysqli_connect code — PHP's equivalent of JDBC).
3. **Fig 3** – The Add Student form filled in with a new student's details.
4. **Fig 4** – The green "Student added successfully" message after saving.
5. **Fig 5** – The Students page listing all records dynamically from MySQL.

## WEEK 11 – Full CRUD and Search
1. **Fig 1** – Edit Student form pre-filled with an existing record.
2. **Fig 2** – The students list showing the updated details after saving.
3. **Fig 3** – The JavaScript confirmation dialog when clicking Delete.
4. **Fig 4** – Search results filtered by Course (e.g. search "Computer Science").
5. **Fig 5** – The "No records found" message after searching gibberish (e.g. "zzzz").
6. **Fig 6** – `students.php` code in your editor showing `mysqli_prepare` and `bind_param`.

## WEEK 12 – Web Forms and Control Structures
1. **Fig 1** – `week12_forms.php` showing the course DropDownList.
2. **Fig 2** – The greeting message after clicking Submit (PostBack) — e.g. "Good afternoon, ... You selected: ...".
3. **Fig 3** – The GridView-equivalent dynamic table of students on the same page.
4. **Fig 4** – The Java vs PHP vs ASP.NET session comparison table.
5. **Fig 5** – Logging in with valid credentials and landing on the protected dashboard.

## WEEK 13 – Simulated LDAP and Role-Based Access
1. **Fig 1** – The Enterprise Login (Simulated LDAP) page.
2. **Fig 2** – `ldap_directory.php` in your editor showing the directory entries (DN, uid, role).
3. **Fig 3** – Logged in as `admin1` — students list WITH Edit/Delete buttons.
4. **Fig 4** – Logged in as `stud1` — the same list WITHOUT Edit/Delete buttons.
5. **Fig 5** – Search by Registration Number returning a matching record.

## WEEK 14 – Testing, Publishing and Maintenance
1. **Fig 1** – `TESTING.md` checklist with all tests ticked.
2. **Fig 2** – Typing `' OR '1'='1` in the login form and getting "Invalid username or password" (SQL injection blocked).
3. **Fig 3** – DevTools responsive mode: students page at 375px (1 col), 800px and 1200px.
4. **Fig 4** – Your GitHub repository page showing the weekly commit history.
5. **Fig 5** – phpMyAdmin Export tab exporting `studentdb.sql`.
6. **Fig 6** – The README.md rendered on your GitHub repository page.
