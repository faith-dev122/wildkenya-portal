<?php
/*
 * Week 11 – DELETE of the CRUD set (after JavaScript confirm() on the
 * list page). Prepared statement + redirect with a success message.
 * Admin-only (Week 13 role-based access control).
 */
require "includes/auth_check.php";
require "config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: students.php?msg=Only+administrators+can+delete+records&type=error");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
    header("Location: students.php?msg=Student+deleted+successfully");
} else {
    header("Location: students.php?msg=Delete+failed+or+record+not+found&type=error");
}
exit;
