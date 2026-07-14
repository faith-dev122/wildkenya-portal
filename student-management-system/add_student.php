<?php
/*
 * Week 10 – INSERT through a web form (Create of CRUD, Week 6 incorporated).
 * Uses a prepared statement, server-side validation, and redirects back
 * to the list with a success/error message (Week 11 requirement).
 */
require "includes/auth_check.php";
require "config/db.php";
if ($_SESSION['role'] !== 'admin') {
    header("Location: students.php?msg=Only+administrators+can+add+records&type=error");
    exit;
}

$courses = ["Computer Science", "Information Technology", "Software Engineering", "Business IT"];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reg_no    = trim($_POST["reg_no"]);
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $course    = trim($_POST["course"]);

    if ($reg_no === "" || $full_name === "" || $email === "" || $course === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO students (reg_no, full_name, email, course) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $reg_no, $full_name, $email, $course);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: students.php?msg=Student+added+successfully");
            exit;
        } else {
            $error = "Could not add student (is the registration number unique?).";
        }
    }
}
include "includes/header.php";
?>
<div class="card" style="max-width:520px;margin:auto;">
  <h1>Add New Student</h1>
  <?php if ($error): ?><div class="msg error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" action="add_student.php">
    <label>Registration Number</label>
    <input type="text" name="reg_no" placeholder="e.g. BSCCS/2024/050" required>

    <label>Full Name</label>
    <input type="text" name="full_name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Course</label>
    <select name="course">
      <?php foreach ($courses as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="btn">Save Student</button>
    <a href="students.php" class="btn secondary">Cancel</a>
  </form>
</div>
<?php include "includes/footer.php"; ?>
