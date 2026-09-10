<?php

session_start();

require_once "../config/config.php";
require_once "../config/database.php";

// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$errors = [];
$success = "";

// Get current user's information
$stmt = $pdo->prepare("
    SELECT user_id, full_name, email, phone, role, password_hash
    FROM users
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // -------------------------
    // BASIC VALIDATION
    // -------------------------

    if ($full_name === "") {
        $errors[] = "Full name is required.";
    }

    if ($email === "") {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }


    // -------------------------
    // CHECK EMAIL DUPLICATE
    // -------------------------

    if (empty($errors)) {

        $email_check = $pdo->prepare("
            SELECT user_id
            FROM users
            WHERE email = ?
            AND user_id != ?
        ");

        $email_check->execute([$email, $user_id]);

        if ($email_check->fetch()) {
            $errors[] = "That email address is already being used by another account.";
        }
    }


    // -------------------------
    // PASSWORD CHANGE
    // -------------------------

    $changing_password = (
        $current_password !== "" ||
        $new_password !== "" ||
        $confirm_password !== ""
    );

    if ($changing_password) {

        if ($current_password === "") {
            $errors[] = "Enter your current password.";
        }

        if ($new_password === "") {
            $errors[] = "Enter a new password.";
        }

        if ($confirm_password === "") {
            $errors[] = "Confirm your new password.";
        }

        if (
            $new_password !== "" &&
            $confirm_password !== "" &&
            $new_password !== $confirm_password
        ) {
            $errors[] = "The new passwords do not match.";
        }

        if (
            $new_password !== "" &&
            strlen($new_password) < 8
        ) {
            $errors[] = "The new password must contain at least 8 characters.";
        }


        // Verify current password
        if (
            $current_password !== "" &&
            empty($errors)
        ) {

            if (!password_verify(
                $current_password,
                $user["password_hash"]
            )) {
                $errors[] = "Your current password is incorrect.";
            }
        }
    }


    // -------------------------
    // UPDATE ACCOUNT
    // -------------------------

    if (empty($errors)) {

        try {

            $pdo->beginTransaction();

            // Update basic information
            $update = $pdo->prepare("
                UPDATE users
                SET full_name = ?,
                    email = ?,
                    phone = ?
                WHERE user_id = ?
            ");

            $update->execute([
                $full_name,
                $email,
                $phone,
                $user_id
            ]);


            // Update password if requested
            if ($changing_password) {

                $new_password_hash = password_hash(
                    $new_password,
                    PASSWORD_DEFAULT
                );

                $password_update = $pdo->prepare("
                    UPDATE users
                    SET password_hash = ?
                    WHERE user_id = ?
                ");

                $password_update->execute([
                    $new_password_hash,
                    $user_id
                ]);
            }


            $pdo->commit();


            // Update session information
            $_SESSION["full_name"] = $full_name;
            $_SESSION["email"] = $email;


            $success = "Your account settings have been updated successfully.";


            // Reload user information
            $stmt = $pdo->prepare("
                SELECT user_id, full_name, email, phone, role, password_hash
                FROM users
                WHERE user_id = ?
            ");

            $stmt->execute([$user_id]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);


        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errors[] = "Something went wrong while updating your account.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Account Settings | Blood Donation System</title>

        <style>

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background: #f4f6f9;
                color: #333;
            }

            .container {
                width: 90%;
                max-width: 800px;
                margin: 50px auto;
            }

            .card {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            }

            h1 {
                margin-top: 0;
                color: #b30000;
            }

            h2 {
                margin-top: 30px;
                color: #444;
                border-bottom: 1px solid #ddd;
                padding-bottom: 10px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            label {
                display: block;
                margin-bottom: 7px;
                font-weight: bold;
            }

            input {
                width: 100%;
                padding: 11px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 15px;
            }

            input:focus {
                outline: none;
                border-color: #b30000;
            }

            .role {
                background: #eee;
                padding: 11px;
                border-radius: 5px;
                text-transform: capitalize;
            }

            button {
                width: 100%;
                padding: 13px;
                background: #b30000;
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
            }

            button:hover {
                background: #8f0000;
            }

            .success {
                background: #d4edda;
                color: #155724;
                padding: 12px;
                border-radius: 5px;
                margin-bottom: 20px;
            }

            .error {
                background: #f8d7da;
                color: #721c24;
                padding: 12px;
                border-radius: 5px;
                margin-bottom: 20px;
            }

            .error ul {
                margin: 5px 0 0;
            }

            .back {
                display: inline-block;
                margin-top: 20px;
                color: #b30000;
                text-decoration: none;
            }

            .back:hover {
                text-decoration: underline;
            }

        </style>

    </head>

    <body>

    <div class="container">

        <div class="card">

            <h1>Account Settings</h1>

            <p>
                Update your account information and password.
            </p>


            <?php if (!empty($success)): ?>

                <div class="success">
                    <?= htmlspecialchars($success) ?>
                </div>

            <?php endif; ?>


            <?php if (!empty($errors)): ?>

                <div class="error">

                    <strong>Please fix the following:</strong>

                    <ul>

                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?= htmlspecialchars($error) ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <form method="POST">


                <!-- ACCOUNT INFORMATION -->

                <h2>Account Information</h2>


                <div class="form-group">

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        value="<?= htmlspecialchars($user["full_name"]) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($user["email"]) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="phone">
                        Phone Number
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($user["phone"]) ?>"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Account Role
                    </label>

                    <div class="role">

                        <?= htmlspecialchars($user["role"]) ?>

                    </div>

                </div>


                <!-- PASSWORD -->

                <h2>Change Password</h2>

                <p>
                    Leave these fields empty if you do not want to change your password.
                </p>


                <div class="form-group">

                    <label for="current_password">
                        Current Password
                    </label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                    >

                </div>


                <div class="form-group">

                    <label for="new_password">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                    >

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                    >

                </div>


                <button type="submit">
                    Save Changes
                </button>


            </form>


            <a class="back" href="login.php">
                Back to Login
            </a>

        </div>

    </div>

    </body>

</html>