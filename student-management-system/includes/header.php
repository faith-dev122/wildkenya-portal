<?php if (session_status() === PHP_SESSION_NONE) { session_start(); }
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<!-- Week 8: viewport meta tag for Mobile-First responsive design -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Management System - BIT3208</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?php echo htmlspecialchars($theme); ?>">
<header class="topbar">
  <div class="brand">🎓 Student Management System</div>
  <nav class="mainnav">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="students.php">Students</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <a href="add_student.php">Add Student</a>
    <?php endif; ?>
      <a href="week12_forms.php">Web Forms</a>
      <a href="logout.php" class="logout">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="ldap_login.php">LDAP Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
