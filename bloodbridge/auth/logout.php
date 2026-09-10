<?php

session_start();

// Remove all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// FIX (linking pass): this file lives in /auth/, but login.php is at the site
// root — "login.php" was resolving to the nonexistent /auth/login.php.
header("Location: ../login.php");
exit;