<?php
/*
 * Week 9 – Protected Dashboard
 *  - Displays the logged-in user, SESSION ID and LOGIN TIME
 *    (Class Exercise 2 requirements).
 *  - Theme toggle stores the preference in a COOKIE (Light/Dark).
 *  - auth_check.php redirects unauthenticated users to login.php.
 */
require "includes/auth_check.php";
require "config/db.php";

// Theme cookie handling (Week 9: cookies for personalisation)
if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark'])) {
    setcookie("theme", $_GET['theme'], time() + (365 * 24 * 60 * 60), "/");
    header("Location: dashboard.php");
    exit;
}

// Quick stats (dynamic HTML generation, Week 10)
$total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];

include "includes/header.php";
?>
<div class="card">
  <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
  <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>
</div>

<div class="grid">
  <div class="card">
    <h2>Session Details (Week 9)</h2>
    <p><strong>Session ID:</strong><br><code><?php echo session_id(); ?></code></p>
    <p><strong>Login Time:</strong> <?php echo htmlspecialchars($_SESSION['login_time']); ?></p>
    <p><strong>Theme cookie:</strong> <?php echo htmlspecialchars($theme); ?></p>
  </div>

  <div class="card">
    <h2>Theme Preference (Cookie)</h2>
    <p>Your choice is remembered by a cookie even after the browser restarts.</p>
    <a class="btn secondary" href="dashboard.php?theme=light">Light</a>
    <a class="btn" href="dashboard.php?theme=dark">Dark</a>
  </div>

  <div class="card">
    <h2>Students Module</h2>
    <p><strong><?php echo $total; ?></strong> students registered in the database.</p>
    <a class="btn" href="students.php">View / Search Students</a>
  </div>
</div>
<?php include "includes/footer.php"; ?>
