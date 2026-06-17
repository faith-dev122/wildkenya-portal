<?php
// ============================================================
// WildKenya — Remember Me Auto-Login Check
// Bonus Feature: Remember Me Functionality
//
// HOW TO USE THIS FILE:
// Open includes/header.php and find the line that starts the session,
// usually: session_start();
// Directly AFTER that line, add this:
//     require_once __DIR__ . '/remember_me.php';
//
// This file checks for a long-lasting "remember me" cookie and
// automatically logs the user back in if the session has expired
// but the cookie is still valid (cookie lasts 30 days).
// ============================================================

// Only run this check if the user is NOT already logged in
if (!isset($_SESSION['user_id']) && isset($_COOKIE['wildkenya_remember'])) {

    // The cookie stores: user_id : verification_token
    $cookie_parts = explode(':', $_COOKIE['wildkenya_remember']);

    if (count($cookie_parts) === 2) {
        $cookie_user_id = (int)$cookie_parts[0];
        $cookie_token   = $cookie_parts[1];

        // Look up the user
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $cookie_user_id);
        $stmt->execute();
        $remembered_user = $stmt->get_result()->fetch_assoc();

        if ($remembered_user) {
            // Recreate the same verification token used when the cookie was set
            // It is derived from the user's password hash, so it becomes invalid
            // automatically if the password is ever changed
            $expected_token = hash_hmac('sha256', (string)$remembered_user['id'], $remembered_user['password']);

            if (hash_equals($expected_token, $cookie_token)) {
                // Token matches — log the user back in automatically
                $_SESSION['user_id']    = $remembered_user['id'];
                $_SESSION['user_name']  = $remembered_user['name'];
                $_SESSION['user_email'] = $remembered_user['email'];
                $_SESSION['user_role']  = $remembered_user['role'];
            } else {
                // Invalid or tampered cookie — remove it
                setcookie('wildkenya_remember', '', time() - 3600, '/');
            }
        }
    }
}
?>
