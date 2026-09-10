<?php

require_once "includes/config.php";
$csrf_token = generateCsrfToken();

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $submitted_token = $_POST["csrf_token"] ?? "";
    
    if (!verifyCsrfToken($submitted_token)) {
        $errors[] = "Invalid security token. Please try again.";
    }

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $role = $_POST["role"] ?? "";
    $password = $_POST["password"] ??"";
    $confirm_password = $_POST["confirm_password"] ??"";

    $blood_group = trim($_POST["blood_group"] ??"");
    $age = trim($_POST["age"] ??"");
    $weight = trim($_POST["weight"] ??"");
    $city = trim($_POST["city"] ??"");

    if ($full_name === "") {
        $errors[] = "Full name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    if ($phone === "") {
        $errors[] = "Phone number is required.";
    }

    $allowed_roles = ["donor","recipient","hospital"];
    if (!in_array($role, $allowed_roles, true)) {
        $errors[] = "Please select a valid account type.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    $allowed_blood_groups = ["A+","A-","B+", "AB+", "AB-", "O+", "O-"];

    if (!in_array($blood_group, $allowed_blood_groups, true)) {
        $errors[] = "Please select a valid blood group.";
    }

    if (!is_numeric($age) || $age < 18 || $age > 100) {
        $errors[] = "Age must be between 18 and 100";
    }

    if (!is_numeric($weight) || $weight <= 0) {
        $errors[] = "Please enter a valid weight.";
    }

    if ($city === "") {
        $errors[] = "City is required";
    }

    if (empty($errors)) {


        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "An account with this email already exists.";
        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $status = "pending";

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password, phone, role, status) VALUES (?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "ssssss", $full_name, $email, $password_hash, $phone, $role, $status);
            mysqli_stmt_execute($stmt);
            $new_user_id = mysqli_insert_id($conn);

            if ($role === "donor") {
                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO donors (user_id, blood_group, age, weight, city) VALUES (?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt, "isiis", $new_user_id, $blood_group, $age, $weight, $city);
                mysqli_stmt_execute($stmt);
            }

            $success = "Account created successfully! You can log in once your account is verified.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width", initial-scale="1.0">

        <title>Create Account | Blood Donation Management System</title>

        <link rel="stylesheet" href="css/style.css">

    </head>

    <body>
       
        <main class="auth-container">
            <section class="auth-card">
                <?php if (!empty($success)): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p 
                    style="color:#990011;
                    font-size:13px;
                    font-family: fraunces;
                    font-weight: 900;
                    margin-bottom: 0px;
                    padding-bottom: 0px;"
                >
                    JOIN BDMS
                </p>
                <h1 >Create your account</h1>

                <form method="POST" action="register.php">
                    
                    <input 
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($csrf_token) ?>"
                    >
                    
                    <div class="form-group role-group">

                        <label 
                            style=
                            "color: #7A6A6C;
                            font-size: 18px;
                            font-family: inter;" 
                            for="role"
                        >I am registering as a...</label>

                        <div class="role-options">

                            <!-- FIX (linking pass): all three radios had "checked" — an invalid radio group where every option claims to be selected. Only "donor" (the sensible default) keeps it now. -->
                            <input
                                type="radio"
                                id="role-donor"
                                name="role"
                                value="donor"
                                checked
                            >

                            <label for="role-donor">
                                Donor
                            </label>

                            <input
                                type="radio"
                                id="role-recipient"
                                name="role"
                                value="recipient"
                            >

                            <label for="role-recipient">
                                Recipient
                            </label>

                            <input
                                type="radio"
                                id="role-hospital"
                                name="role"
                                value="hospital"
                            >

                            <label for="role-hospital">
                                Hospital
                            </label>

                        </div>

                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>

                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>

                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                required
                            >
                        </div>
                    
                    </div>

                    <div class="form-group">
                            <label for="email">Email Address</label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                required
                            >
                    </div>

                    <div class="form-row">
                        <div class="form-group">

                            <label for="password">Password</label>

                            <input 
                                type="password"
                                id="password"
                                name="password"
                                required
                            >

                        </div>

                        <div class="form-group">

                            <label for="password">Confirm Password</label>

                            <input 
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                required
                            >

                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">

                            <label for="blood_group">Blood Group</label>

                            <select id="blood_group" name="blood_group" required>

                                <option value="">Select blood group</option>

                                <option value="A+">A+</option>

                                <option value="A-">A-</option>

                                <option value="B+">B+</option>

                                <option value="B-">B-</option>

                                <option value="AB+">AB+</option>

                                <option value="AB-">AB-</option>

                                <option value="O+">O+</option>

                                <option value="O-">O-</option>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="age">Age</label>

                            <input

                                type="number"

                                id="age"

                                name="age"

                                min="18"

                                max="100"

                                required

                            >

                        </div>

                    </div>

                    <div class="form-row">

                        <div class="form-group">

                            <label for="weight">Weight (KG)</label>

                            <input

                                type="number"

                                id="weight"

                                name="weight"

                                step="0.1"

                                min="1"

                                required

                            >

                        </div>

                        <div class="form-group">

                            <label for="city">City</label>

                            <input

                                type="text"

                                id="city"

                                name="city"

                                required

                            >

                        </div>

                    </div>

                    <button type="submit" name="register">
                        Create Account
                    </button>

                    <div class="login_tag">
                        <p style="color:#4a4042;">Already have an account? 
                            <a href="login.php"
                                style="color:#990011;">Log In</a>
                        </p> 
                    </div>

                </form>

            </section>

        </main>

        <footer class="site-footer">

            <?php
            require_once "./includes/public_footer.php";
            ?>
        </footer>

    </body>
</html>