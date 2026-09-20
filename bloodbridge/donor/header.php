<?php
$page_title = $page_title ?? "Donor";
$donor_name = $_SESSION["full_name"] ?? "Donor";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | BloodBridge</title>
    <link rel="stylesheet" href="../@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="donor.css">
</head>

<body class="donor-shell">
    <header class="role-header role-header--donor">
        <div class="role-header__inner">
            <a class="brand brand--donor" href="dashboard.php">
                <i class="fa-solid fa-droplet" aria-hidden="true"></i>
                <span>BloodBridge</span>
            </a>
            <nav class="role-header__nav" aria-label="Donor navigation">
                <span class="role-user"><?= htmlspecialchars($donor_name) ?></span>
                <a href="dashboard.php">Dashboard</a>
                <a class="role-logout" href="../logout.php">Log Out</a>
            </nav>
        </div>
    </header>