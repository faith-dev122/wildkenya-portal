<?php
// ============================================================
// WildKenya — Database Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP default is no password
define('DB_NAME', 'wildkenya');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("<div style='font-family:sans-serif; padding:20px; color:red;'>
         <h3>Database Connection Failed</h3>
         <p>" . $conn->connect_error . "</p>
         <p>Make sure XAMPP is running and you have imported wildkenya.sql</p>
         </div>");
}

// Set character set
$conn->set_charset("utf8mb4");
?>
