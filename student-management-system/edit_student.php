<?php
/*
 * Week 11 – UPDATE of the CRUD set. The form is pre-filled with the
 * existing record (SELECT), then an UPDATE runs on submit — both with
 * prepared statements. Admin-only (Week 13 role-based access).
 */
require "includes/auth_check.php";
require "config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: students.php?msg=Only+administrators+can+edit+records&type=error");
    exit;
}

$courses = ["Computer Science", "Information Technology", "Software Engineering", "Business IT"];
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reg_no    = trim($_POST["reg_no"]);
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $course    = trim($_POST["course"]);

    if ($reg_no === "" || $full_name === "" || $email === "") {
        $error = "All fields are required.";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE students SET reg_no=?, full_name=?, email=?, course=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $reg_no, $full_name, $email, $course, $id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: students.php?msg=Student+updated+successfully");
            exit;
        }
        $error = "Update failed: " . mysqli_error($conn);
    }
}

// Fetch existing record to pre-fill the form
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$student) {
    header("Location: students.php?msg=Student+not+found&type=error");
    exit;
}
include "includes/header.php";
?>
<div class="card" style="max-width:520px;margin:auto;">
  <h1>Edit Student</h1>
  <?php if ($error): ?><div class="msg error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" action="edit_student.php">
    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

    <label>Registration Number</label>
    <input type="text" name="reg_no" value="<?php echo htmlspecialchars($student['reg_no']); ?>" required>

    <label>Full Name</label>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

    <label>Course</label>
    <select name="course">
      <?php foreach ($courses as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>" <?php if ($student['course'] === $c) echo "selected"; ?>>
          <?php echo htmlspecialchars($c); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="btn">Update Student</button>
    <a href="students.php" class="btn secondary">Cancel</a>
  </form>
</div>
<?php include "includes/footer.php"; ?>
