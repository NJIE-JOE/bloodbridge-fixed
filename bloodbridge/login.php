<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once "includes/config.php";

$csrf_token = generateCsrfToken();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
    $submitted_token = $_POST["csrf_token"] ?? "";
    
    if (!verifyCsrfToken($submitted_token)) {
        $errors[] = "Invalid security token. Please try again.";
    }

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {

       
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password, role, status FROM users WHERE email = ? LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user) {
            $errors[] = "Invalid email or password.";
        } elseif ($user["status"] !== "active") {
            $errors[] = "Your account is not active.";
        } elseif (!password_verify($password, $user["password"])) {
            $errors[] = "Invalid email or password.";
        } else {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

          
            switch ($user["role"]) {
                case "donor":
                    header("Location: auth&validation/donor/dashboard.php");
                    exit;
                case "recipient":
                    header("Location: recipient_folder/search-donors.php");
                    exit;
                case "hospital":
                    header("Location: auth&validation/hospital/dashboard.php");
                    exit;
                case "admin":
                    header("Location: bdms-admin/bdms-admin/admin/dashboard.php");
                    exit;
                default:
                    session_destroy();
                    $errors[] = "Invalid account role.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
        >
        <title>Login | Blood Donation Management System</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <main class="auth-container">
            <section class="auth-card">
                <h1>Welcome Back</h1>
                <p>
                    Login to your Blood Donation Management System account.
                </p>

                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <?php foreach ($errors as $error): ?>
                            <p>
                                <?= htmlspecialchars($error) ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">

                    <input 
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($csrf_token) ?>"
                    >

                    <div class="form-group">

                        <label for="email">
                            Email Address
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        name="login"
                    >
                        Login
                    </button>

                    <div class="auth-links">

                        <a href="forgot-password.php">
                            Forgot Password?
                        </a>
                        <a href="register.php">
                            Create an Account
                        </a>

                    </div>
                </form>
            </section>
        </main>
        <?php
            require_once "./includes/public_footer.php";
        ?>
    </body>
</html>