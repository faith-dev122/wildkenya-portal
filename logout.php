<?php
// ============================================================
// WildKenya — Logout (logout.php)
// Destroys session AND clears the Remember Me cookie
// ============================================================
session_start();

// Clear the remember me cookie if it exists
if (isset($_COOKIE['wildkenya_remember'])) {
    setcookie('wildkenya_remember', '', time() - 3600, '/');
}

// Destroy the session
$_SESSION = [];
session_destroy();

header("Location: pages/login.php");
exit();
?>
