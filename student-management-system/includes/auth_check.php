<?php
/*
 * Week 9 – Session Management (Java HttpSession -> PHP $_SESSION)
 * Include this file at the top of every protected page.
 * If there is no active session the user is redirected to login.php,
 * exactly like the "redirect unauthenticated users" requirement.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please+log+in+first");
    exit;
}

// Theme preference stored in a COOKIE (Week 9: cookies for personalisation)
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
?>
