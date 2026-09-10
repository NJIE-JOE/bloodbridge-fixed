<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BloodBridge</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/header.css">
<link rel="stylesheet" href="../css/footer.css">
<link rel="stylesheet" href="/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body>


    <header id="header">
        <div class="container1">
            <nav>
                <a href="#" class="logo">
                    <i class="fa-solid fa-droplet"></i>
                    BloodBridge
                </a>
                <ul class="first-ul">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="eligibility.php">Eligibility</a>
                    </li>
                    <li>
                        <a href="compatibility.php">Compatibility</a>
                    </li>
                    <li>
                        <a href="donationcenter.php">Donation Center</a>
                    </li>
                </ul>
                <ul class="second-ul">
                    <li>
                        <button class="header-btn" type="button" onclick="window.location.href='login.php'">Log In</button>
                    </li>
                    <li>
                        <button class="header-btn" type="button" onclick="window.location.href='register.php'">
                            Get started
                        </button>
                    </li>
                </ul>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
        <div class="nav-backdrop" id="navBackdrop"></div>
    </header>
