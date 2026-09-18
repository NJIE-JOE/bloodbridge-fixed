<?php
session_start();

/*
|--------------------------------------------------------------------------
| Check if user is logged in
|--------------------------------------------------------------------------
*/

function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {
        /*
         * Role pages live one directory below the project root, so the shared
         * login page is one level above them.
         */
        header("Location: ../login.php");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Get currently logged-in user's role
|--------------------------------------------------------------------------
*/

function getUserRole()
{
    return $_SESSION["role"] ?? null;
}

/*
|--------------------------------------------------------------------------
| Require a specific role
|--------------------------------------------------------------------------
*/

function requireRole($allowedRoles)
{
    requireLogin();

    $userRole = getUserRole();

    if (!in_array($userRole, $allowedRoles, true)) {
        http_response_code(403);
        echo "<h1>403 - Access Denied</h1>";
        echo "<p>You do not have permission to access this page.</p>";
        exit;
    }
}

/* Teammates now just get to simply do 
require_once "../includes/auth.php";"
requireLogin();
instead of every teammate writing complicated authentication code */