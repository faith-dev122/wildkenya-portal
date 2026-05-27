<?php
// ============================================================
// WildKenya — Logout (logout.php)
// ============================================================
session_start();
session_destroy();
header("Location: /wildkenya/pages/login.php?msg=logged_out");
exit();
?>
