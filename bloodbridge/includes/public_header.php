<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo safe($page_title); ?> - BloodBridge</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<!--
    FIX (linking pass): this used to load header.css three times (css/header.css,
    /css/header.css, and a stray root-level header.css that's a byte-for-byte
    duplicate of css/header.css) plus a footer.css link with a typo'd
    rel="styelsheet" that browsers just ignore. Trimmed to one link per
    stylesheet. The duplicate root-level header.css file itself was left in
    place, just unreferenced.
-->
<link rel="stylesheet" href="css/design-system.css">
<link rel="stylesheet" href="css/header.css">
<link rel="stylesheet" href="css/footer.css">
<link rel="stylesheet" href="css/donationcenters.css">
<link rel="stylesheet" href="css/style.css">
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
                        <a href="index.php">home</a>
                    </li>
                    <li>
                        <a href="eligibility.php">eligibility</a>
                    </li>
                    <li>
                        <a href="compatibility.php">compatibility</a>
                    </li>
                    <li>
                        <a href="donationcenter.php">donation center</a>
                    </li>
                    <li>
                        <a href="bdms-admin/bdms-admin/login.php">admin</a>
                    </li>
                </ul>
                <ul class="second-ul">
                    <li>
                        <button class="header-btn" type="button" onclick="window.location.href='login.php'">Log In</button>
                    </li>
                    <li>
                        <button class="header-btn" type="button" onclick="window.location.href='register.php'">
                            get started
                        </button>
                    </li>
                    <li>
                        <button class="header-btn" type="button" onclick="window.location.href='bdms-admin/bdms-admin/login.php'">
                            admin portal
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
