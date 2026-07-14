<?php
/*
 * Week 9 – Session Management and Cookies (adapted from Java HttpSession)
 *  - Successful login creates a PHP session ($_SESSION), the equivalent
 *    of request.getSession() in a Java Servlet.
 *  - "Remember Me": a cookie stores the username for 30 days so it is
 *    auto-filled the next time the browser opens (Week 9 assignment).
 * Week 12 – credentials are verified against the DATABASE, then the
 *  user is redirected to a protected dashboard (ASP.NET exercise done in PHP).
 */
session_start();
require "config/db.php";

// Already logged in? Straight to dashboard.
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$info  = isset($_GET['msg']) ? $_GET['msg'] : "";
// Remember-Me cookie: pre-fill the username field
$remembered = isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($username === "" || $password === "") {
        $error = "Username and password are required.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, password, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])) {
            // ---- SESSION CREATED (Week 9) ----
            session_regenerate_id(true);              // session protection (Week 14)
            $_SESSION["user_id"]    = $user["id"];
            $_SESSION["username"]   = $username;
            $_SESSION["full_name"]  = $user["full_name"];
            $_SESSION["role"]       = $user["role"];
            $_SESSION["login_time"] = date("Y-m-d H:i:s");

            // ---- REMEMBER ME COOKIE (Week 9) ----
            if (isset($_POST["remember"])) {
                setcookie("remember_user", $username, time() + (30 * 24 * 60 * 60), "/"); // 30 days
            } else {
                setcookie("remember_user", "", time() - 3600, "/"); // clear it
            }

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
include "includes/header.php";
?>
<div class="card" style="max-width:460px;margin:auto;">
  <h1>Login</h1>
  <?php if ($info):  ?><div class="msg info"><?php echo htmlspecialchars($info); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="msg error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" action="login.php">
    <label>Username</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($remembered); ?>" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label style="font-weight:normal;">
      <input type="checkbox" name="remember" <?php echo $remembered ? "checked" : ""; ?>>
      Remember Me for 30 days
    </label>

    <button type="submit" class="btn">Login</button>
  </form>
  <p style="margin-top:12px;">No account? <a href="register.php">Register</a> |
     Enterprise login: <a href="ldap_login.php">LDAP Login (Week 13)</a></p>
</div>
<?php include "includes/footer.php"; ?>
