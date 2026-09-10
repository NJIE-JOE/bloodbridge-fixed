<?php

require_once "../includes/auth.php";

requireRole(["recipient"]);

?>

<!DOCTYPE html>
<html lang="en">

    <head>
    
        <meta charset="UTF-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >
        <title>Recipient Dashboard</title>
    
    </head>
    
    <body>

        <h1>Recipient Dashboard</h1>

        <p>
            Welcome,
            <?= htmlspecialchars($_SESSION["full_name"]) ?>!
        </p>

        <p>
            You are logged in as:
            <?= htmlspecialchars($_SESSION["role"]) ?>
        </p>

        <!-- FIX (linking pass): was "../auth/logout.php" -- relative to this file that resolved to auth&validation/auth/logout.php, which does not exist. The real logout.php lives at the project root, under /auth/. --><a href="../../auth/logout.php">
            Logout
        </a>
    </body>
</html>