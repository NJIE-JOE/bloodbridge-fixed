<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/database.php";
require_once __DIR__ . "/../config/config.php";
require_once "../includes/csrf.php";

$csrf_token = generateCsrfToken();

$message = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $submitted_token = $_POST["csrf_token"] ?? "";
        
        if (!verifyCsrfToken($submitted_token)) {
            $errors[] = "Invalid security token. Please try again.";
        
        } else {

            $email = trim($_POST["email"] ?? "");
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $errors[] = "Please enter a valid email address.";

            } else {

                $stmt = $pdo->prepare(

                    "SELECT user_id FROM users WHERE email = ? LIMIT 1"
    
                );

                $stmt->execute([$email]);

                $user = $stmt->fetch();

                /*
                * We deliberately give the same response
                * whether the account exists or not.
                */

                $message =
                    "If an account with that email exists, "
                    . "a password reset link has been generated.";

                if ($user) {
                    /*
                    * Generate a cryptographically secure token.
                    */

                    $reset_token = bin2hex(
                        random_bytes(32)
                    );

                    //Generate a secure reset token
                    $token = bin2hex(random_bytes(32));

                    /*
                    * Store only the HASH of the token.
                    */

                    $token_hash = hash(
                        "sha256",
                        $reset_token
                    );

                    $token_hash = hash(
                        "sha256",
                        $token
                    );

                    /*
                    * Token expires after 30 minutes.
                    */

                    $expires_at = date(
                        "Y-m-d H:i:s",
                        time() + (30 * 60)
                    );



                    /*
                    * Remove previous unused tokens
                    * belonging to this user.
                    */

                    $delete = $pdo->prepare(
                        "DELETE FROM password_resets
                        WHERE user_id = ?"
                    );

                    $delete->execute([
                        $user["user_id"]
                    ]);

                    /*
                    * Store the new token.
                    */

                    $insert = $pdo->prepare(
                        "INSERT INTO password_resets
                        (user_id, token_hash, expires_at)
                        VALUES (?, ?, ?)"
                    );

                    $insert->execute([
                        $user["user_id"],
                        $token_hash,
                        $expires_at
                    ]);

                    /*
                    * DEVELOPMENT ONLY:
                    * Generate the reset URL so we can test it locally.
                    */

                    $reset_link = BASE_URL ."auth/reset/password.php?token=" .urlencode($token);

                        /*
                        * For now we display the link locally.
                        *
                        * In production this will be replaced
                        * by an email service.
                        */

                    $message .=
                        "<br><br>"
                        . "<strong>Development Reset Link:</strong><br>"
                        . "<a href=\""
                        . htmlspecialchars($reset_link)
                        . "\">"
                        . htmlspecialchars($reset_link)
                        . "</a>";
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
        
        <title>Forgot Password | Blood Donation Management System</title>
    
    </head>
    
    <body>
        <main>
            <section>

                <h1>Forgot Password?</h1>

                <p>
                    Enter your email address to reset your password.
                </p>

                <?php if (!empty($errors)): ?>
                
                <div>

                    <?php foreach ($errors as $error): ?>
                    
                    <p>
                    <?= htmlspecialchars($error) ?>
                    </p>

                    <?php endforeach; ?>
                
                </div>
                
                <?php endif; ?>

                <?php if ($message !== ""): ?>

                    <div>
                        <p>
                            <?= $message ?>
                        </p>
                    </div>
                <?php endif; ?>

                <form
                    method="POST"
                    action="forgot-password.php"
                >
                
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($csrf_token) ?>"
                    >
                
                    <div>
                    
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

                    <button type="submit">
                        Generate Reset Link
                    </button>

                </form>
                
                <p>
                    <a href="login.php">
                        Back to Login
                    </a>
                </p>
            
            </section>
        
        </main>
    
    </body>

</html>