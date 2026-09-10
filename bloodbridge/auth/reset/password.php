<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../../config/database.php";
require_once "../../includes/csrf.php";

$csrf_token = generateCsrfToken();

$errors = [];
$success = "";

// Get the token from the URL
$token = $_GET["token"] ?? "";

// Make sure a token was provided
if ($token === "") {
    $errors[] = "Invalid password reset link.";

} else {

    // Hash the token from the URL
    $token_hash = hash(
        "sha256",
        $token
    );
    
    // Find the token in the database
    $stmt = $pdo->prepare(
        "SELECT pr_id, user_id, expires_at, used_at
        FROM password_resets
        WHERE token_hash = ?
        LIMIT 1"
    );

    $stmt->execute([
        $token_hash
    ]);

    $reset = $stmt->fetch();

    // Check if token exists
    if (!$reset) {

        $errors[] =
            "This password reset link is invalid.";
    }

    // Check if token has already been used
    elseif ($reset["used_at"] !== null) {

        $errors[] =
            "This password reset link has already been used.";
    }

    // Check if token has expired
    elseif (
        strtotime($reset["expires_at"]) < time()
    ) {

        $errors[] =
            "This password reset link has expired.";
    }
}

// Process the new password
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && empty($errors)
) {
    
    // Check CSRF token
    $submitted_csrf =
        $_POST["csrf_token"] ?? "";

    if (!verifyCsrfToken($submitted_csrf)) {
        $errors[] =
            "Invalid security token. Please try again.";

    } else {

        $password =
            $_POST["password"] ?? "";
        $confirm_password =
            $_POST["confirm_password"] ?? "";

        // Check password length
        if (strlen($password) < 8) {

            $errors[] =
                "Password must be at least 8 characters.";
        }

        // Check that both passwords match
        if ($password !== $confirm_password) {

            $errors[] =
                "Passwords do not match.";
        }

        // If there are no errors, change the password
        if (empty($errors)) {
            
            // Hash the new password
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Update the user's password
            $update = $pdo->prepare(
                "UPDATE users
                SET password_hash = ?
                WHERE user_id = ?"
            );

            $update->execute([
                $password_hash,
                $reset["user_id"]
            ]);

            // Mark the reset token as used
            $mark_used = $pdo->prepare(
                "UPDATE password_resets
                SET used_at = NOW()
                WHERE pr_id = ?"
            );

            $mark_used->execute([
                $reset["pr_id"]
            ]);

            $success =
                "Your password has been reset successfully.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
    </head>

    <body>

        <main>

            <h1>Reset Password</h1>

                <?php if (!empty($errors)): ?>

                <div>
                        <?php foreach ($errors as $error): ?>

                        <p>
                            <?= htmlspecialchars($error) ?>
                        </p>

                        <?php endforeach; ?>
                </div>

                <?php endif; ?>

                <?php if ($success !== ""): ?>
                    
                    <div>

                        <p>
                            <?= htmlspecialchars($success) ?>
                        </p>

                        <p>
                            <a href="login.php">
                                Go to Login
                            </a>
                        </p>

                    </div>

                <?php elseif (empty($errors)): ?>

                    <form 
                        method="POST" 
                        action=""
                    >

                        <input 
                            type="hidden"
                            name="csrf_token" 
                            value="<?= htmlspecialchars($csrf_token) ?>"
                        >

                        <div>

                            <label for="password">
                                New Password
                            </label>

                            <br>

                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                            >

                        </div>

                        <br>

                        <div>

                            <label for="confirm_password">
                                Confirm New Password
                            </label>

                            <br>

                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required
                            >

                        </div>

                        <br>

                        <button type="submit">
                            Reset Password
                        </button>

                    </form>

                <?php endif; ?>

        </main>

    </body>

</html>