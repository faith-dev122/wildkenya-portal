<?php
/*
 * Week 7 incorporated: User Registration with password_hash().
 * Week 12 concept: the course <select> is PHP's equivalent of the
 * ASP.NET DropDownList server control, and the PHP "echo" of options
 * from an array demonstrates control structures (foreach loop).
 */
session_start();
require "config/db.php";

$courses = ["Computer Science", "Information Technology", "Software Engineering", "Business IT"];
$error = ""; $success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $username  = trim($_POST["username"]);
    $password  = $_POST["password"];
    $confirm   = $_POST["confirm"];

    // Week 14 concept: input validation before touching the database
    if ($full_name === "" || $email === "" || $username === "" || $password === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Prepared statement (SQL-injection protection, Weeks 6/11/14)
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($check, "ss", $username, $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Username or email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);  // Week 7: hashing
            $stmt = mysqli_prepare($conn,
                "INSERT INTO users (full_name, email, username, password, role) VALUES (?, ?, ?, ?, 'student')");
            mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $username, $hashed);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Account created successfully. You can now log in.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
include "includes/header.php";
?>
<div class="card" style="max-width:520px;margin:auto;">
  <h1>Create Account</h1>
  <?php if ($error):   ?><div class="msg error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <?php if ($success): ?><div class="msg success"><?php echo htmlspecialchars($success); ?> <a href="login.php">Login here</a></div><?php endif; ?>

  <form method="POST" action="register.php">
    <label>Full Name</label>
    <input type="text" name="full_name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Username</label>
    <input type="text" name="username" required>

    <label>Course</label>
    <select name="course">
      <?php foreach ($courses as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
      <?php endforeach; ?>
    </select>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Confirm Password</label>
    <input type="password" name="confirm" required>

    <button type="submit" class="btn">Register</button>
  </form>
</div>
<?php include "includes/footer.php"; ?>
