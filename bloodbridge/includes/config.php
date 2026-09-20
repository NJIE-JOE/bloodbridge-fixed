<?php

session_start();


require_once __DIR__ . "/csrf.php";

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "bdms";


$conn = mysqli_connect($db_host, $db_user, $db_pass);
if (!$conn) {
    die("Could not connect to MySQL: " . mysqli_connect_error());
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db_name");
mysqli_select_db($conn, $db_name);

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(30),
    role VARCHAR(20),
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    blood_group VARCHAR(5),
    age INT,
    weight DECIMAL(5,2),
    city VARCHAR(60),
    available TINYINT DEFAULT 1,
    last_donation_date DATE
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(120),
    address VARCHAR(150),
    city VARCHAR(60),
    license_number VARCHAR(60)
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT,
    hospital_id INT,
    blood_group VARCHAR(5),
    units INT,
    urgency VARCHAR(10),
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS donation_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT,
    hospital_id INT,
    units INT,
    donated_at DATE
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT,
    hospital_id INT,
    appointment_time DATETIME,
    status VARCHAR(20) DEFAULT 'scheduled'
)");

mysqli_query($conn, "ALTER TABLE appointments ADD COLUMN location VARCHAR(150) NULL");
mysqli_query($conn, "ALTER TABLE appointments ADD UNIQUE KEY uq_hospital_appointment_time (hospital_id, appointment_time)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    blood_group VARCHAR(5),
    units INT DEFAULT 0
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS camps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    camp_date DATE,
    location VARCHAR(150),
    description TEXT,
    created_by INT
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    token VARCHAR(64),
    expires_at DATETIME
)");

mysqli_query($conn, "ALTER TABLE hospitals ADD COLUMN verified_at DATETIME NULL");
mysqli_query($conn, "ALTER TABLE inventory ADD UNIQUE KEY uq_hospital_blood_group (hospital_id, blood_group)");
mysqli_query($conn, "ALTER TABLE donors MODIFY COLUMN weight DECIMAL(5,2)");
mysqli_query($conn, "ALTER TABLE password_resets ADD COLUMN used_at DATETIME NULL");


$check = mysqli_query($conn, "SELECT id FROM users LIMIT 1");
if (mysqli_num_rows($check) == 0) {

    $hashed = password_hash("password123", PASSWORD_DEFAULT);

    // 1 admin
    mysqli_query($conn, "INSERT INTO users (name, email, password, phone, role, status) VALUES
        ('Admin User', 'admin@bdms.test', '$hashed', '670000001', 'admin', 'active')");

    // 2 donors
    mysqli_query($conn, "INSERT INTO users (name, email, password, phone, role, status) VALUES
        ('Achu Ngwa', 'achu@bdms.test', '$hashed', '670000002', 'donor', 'active')");
    $donor1_user_id = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO users (name, email, password, phone, role, status) VALUES
        ('Delphine Ashu', 'delphine@bdms.test', '$hashed', '670000003', 'donor', 'pending')");
    $donor2_user_id = mysqli_insert_id($conn);


    // 1 recipient
    mysqli_query($conn, "INSERT INTO users (name, email, password, phone, role, status) VALUES
        ('Brenda Fon', 'brenda@bdms.test', '$hashed', '670000004', 'recipient', 'active')");
    $recipient_id = mysqli_insert_id($conn);

    
    // 1 hospital (user account + matching hospitals row)
    mysqli_query($conn, "INSERT INTO users (name, email, password, phone, role, status) VALUES
        ('Buea General Hospital', 'buea.general@bdms.test', '$hashed', '670000005', 'hospital', 'active')");
    $hospital1_user_id = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO hospitals (user_id, name, address, city, license_number) VALUES
        ($hospital1_user_id, 'Buea General Hospital', '123 Molyko Road', 'Buea', 'LIC-0001')");
    $hospital1_id = mysqli_insert_id($conn);

    $donor1_id = $donor1_user_id;

    // inventory for the active hospital
    $blood_groups = array('O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-');
    $sample_units = array(24, 6, 18, 9, 14, 4, 10, 3);
    for ($i = 0; $i < count($blood_groups); $i++) {
        $bg = $blood_groups[$i];
        $u = $sample_units[$i];
        mysqli_query($conn, "INSERT INTO inventory (hospital_id, blood_group, units) VALUES ($hospital1_id, '$bg', $u)");
    }


    for ($w = 8; $w >= 1; $w--) {
        $units = rand(1, 4);
        mysqli_query($conn, "INSERT INTO donation_history (donor_id, hospital_id, units, donated_at)
            VALUES ($donor1_id, $hospital1_id, $units, DATE_SUB(CURDATE(), INTERVAL " . ($w * 7) . " DAY))");
    }

    // a couple of requests spread over recent weeks
    mysqli_query($conn, "INSERT INTO requests (recipient_id, hospital_id, blood_group, units, urgency, status, created_at) VALUES
        ($recipient_id, $hospital1_id, 'O+', 2, 'high', 'pending', DATE_SUB(NOW(), INTERVAL 3 DAY))");
    mysqli_query($conn, "INSERT INTO requests (recipient_id, hospital_id, blood_group, units, urgency, status, created_at) VALUES
        ($recipient_id, $hospital1_id, 'A-', 1, 'medium', 'fulfilled', DATE_SUB(NOW(), INTERVAL 12 DAY))");

    // one upcoming appointment
    mysqli_query($conn, "INSERT INTO appointments (donor_id, hospital_id, appointment_time, status) VALUES
        ($donor1_id, $hospital1_id, DATE_ADD(NOW(), INTERVAL 3 DAY), 'scheduled')");

    // one donation camp
    mysqli_query($conn, "INSERT INTO camps (name, camp_date, location, description, created_by) VALUES
        ('Buea Town Hall Drive', DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Buea Town Hall', 'Open to all eligible donors, walk-ins welcome.', 1)");
}


function safe($text) {
    return htmlspecialchars($text);
}

function clean($conn, $text) {

    return mysqli_real_escape_string($conn, trim($text));
}

function make_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
    }
    return $_SESSION['csrf_token'];
}

function check_csrf_token() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
        die("Something went wrong, please go back and try again.");
    }
}
?>
