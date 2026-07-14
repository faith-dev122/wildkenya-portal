<?php
/*
 * Week 10 – Database Connectivity
 * In the Java notes this is done with JDBC (DriverManager.getConnection).
 * In PHP + XAMPP we use mysqli_connect(). Default XAMPP credentials:
 * host = localhost, user = root, password = "" (empty).
 */
$conn = mysqli_connect("localhost", "root", "", "studentdb");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
