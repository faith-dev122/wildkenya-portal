<?php
/*
 * Week 13 – Simulated LDAP Authentication (Class Exercise 1)
 * Authentication is verified against the SIMULATED DIRECTORY in
 * ldap_directory.php — completely separate from the application's
 * studentdb database. On success a session is created with the role
 * from the directory entry (role-based access control).
 *
 * Test accounts:
 *   admin1 / Admin@2026     (role: admin  – full CRUD)
 *   stud1  / Student@2026   (role: student – view & search only)
 */
session_start();
require "ldap_directory.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid      = trim($_POST["uid"]);
    $password = $_POST["password"];

    $entry = ldap_simulated_bind($uid, $password);   // simulated ldap_bind()

    if ($entry) {
        session_regenerate_id(true);
        $_SESSION["user_id"]    = $entry["dn"];      // the DN identifies the user
        $_SESSION["username"]   = $entry["uid"];
        $_SESSION["full_name"]  = $entry["cn"];
        $_SESSION["role"]       = $entry["role"];
        $_SESSION["login_time"] = date("Y-m-d H:i:s");
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "LDAP bind failed: invalid credentials.";
    }
}
include "includes/header.php";
?>
<div class="card" style="max-width:460px;margin:auto;">
  <h1>Enterprise Login (Simulated LDAP)</h1>
  <p style="margin-bottom:10px;">Authenticates against a central directory
     (<code>dc=university,dc=ac,dc=ke</code>) instead of the application database —
     the same account could log into the portal, library and finance systems.</p>
  <?php if ($error): ?><div class="msg error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" action="ldap_login.php">
    <label>Directory UID</label>
    <input type="text" name="uid" placeholder="e.g. admin1" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit" class="btn">Bind &amp; Login</button>
  </form>
  <p style="margin-top:12px;">Normal login: <a href="login.php">Database login</a></p>
</div>
<?php include "includes/footer.php"; ?>
