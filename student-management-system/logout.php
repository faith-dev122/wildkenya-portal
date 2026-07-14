<?php
/*
 * Week 9 – Logout: invalidate the session.
 * Java: session.invalidate()  ->  PHP: session_unset() + session_destroy()
 * The Remember-Me cookie is left alone so the username still auto-fills,
 * but the session cookie is removed so protected pages redirect to login.
 */
session_start();
session_unset();
session_destroy();
setcookie(session_name(), "", time() - 3600, "/");
header("Location: login.php?msg=You+have+been+logged+out");
exit;
