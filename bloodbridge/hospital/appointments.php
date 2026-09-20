<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
requireRole(["hospital"]);

$user_id = (int) $_SESSION["user_id"];
$status_stmt = mysqli_prepare($conn, "SELECT status FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($status_stmt, "i", $user_id);
mysqli_stmt_execute($status_stmt);
$account = mysqli_fetch_assoc(mysqli_stmt_get_result($status_stmt)) ?: [];
if (($account["status"] ?? "pending") !== "active") {
    http_response_code(403);
    exit("Your hospital account is awaiting administrator verification.");
}

$hospital_stmt = mysqli_prepare($conn, "SELECT id, name, city FROM hospitals WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($hospital_stmt, "i", $user_id);
mysqli_stmt_execute($hospital_stmt);
$hospital = mysqli_fetch_assoc(mysqli_stmt_get_result($hospital_stmt)) ?: [];
$hospital_id = (int) ($hospital["id"] ?? 0);
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "schedule") {
        $donor_id = (int) ($_POST["donor_id"] ?? 0);
        $appointment_time = trim($_POST["appointment_time"] ?? "");
        $location = trim($_POST["location"] ?? ($hospital["city"] ?? ""));
        $time = strtotime($appointment_time);
        if (!$donor_id || !$time || $time < time() || $location === "") {
            $error = "Choose an eligible donor, a future date and time, and a location.";
        } else {
            $eligible_stmt = mysqli_prepare($conn, "SELECT d.id FROM donors d WHERE d.id = ? AND d.available = 1 AND (d.last_donation_date IS NULL OR DATE_ADD(d.last_donation_date, INTERVAL 90 DAY) <= CURDATE()) LIMIT 1");
            mysqli_stmt_bind_param($eligible_stmt, "i", $donor_id);
            mysqli_stmt_execute($eligible_stmt);
            if (!mysqli_fetch_assoc(mysqli_stmt_get_result($eligible_stmt))) {
                $error = "That donor is not currently eligible or available.";
            } else {
                $appointment_time = date("Y-m-d H:i:s", $time);
                $insert = mysqli_prepare($conn, "INSERT INTO appointments (donor_id, hospital_id, appointment_time, location, status) VALUES (?, ?, ?, ?, 'scheduled')");
                mysqli_stmt_bind_param($insert, "iiss", $donor_id, $hospital_id, $appointment_time, $location);
                if (mysqli_stmt_execute($insert)) {
                    $message = "Appointment scheduled.";
                } else {
                    $error = mysqli_errno($conn) === 1062 ? "That hospital time slot is already booked." : "Could not schedule the appointment.";
                }
            }
        }
    }

    if ($action === "status") {
        $appointment_id = (int) ($_POST["appointment_id"] ?? 0);
        $status = $_POST["status"] ?? "";
        $allowed = ["scheduled", "completed", "cancelled", "no-show"];
        if (in_array($status, $allowed, true)) {
            $previous_stmt = mysqli_prepare($conn, "SELECT donor_id, status FROM appointments WHERE id = ? AND hospital_id = ? LIMIT 1");
            mysqli_stmt_bind_param($previous_stmt, "ii", $appointment_id, $hospital_id);
            mysqli_stmt_execute($previous_stmt);
            $previous = mysqli_fetch_assoc(mysqli_stmt_get_result($previous_stmt));
            $update = mysqli_prepare($conn, "UPDATE appointments SET status = ? WHERE id = ? AND hospital_id = ?");
            mysqli_stmt_bind_param($update, "sii", $status, $appointment_id, $hospital_id);
            mysqli_stmt_execute($update);
            if ($previous && $status === "completed" && $previous["status"] !== "completed") {
                $donor_id = (int) $previous["donor_id"];
                $record = mysqli_prepare($conn, "INSERT INTO donation_history (donor_id, hospital_id, units, donated_at) VALUES (?, ?, 1, CURDATE())");
                mysqli_stmt_bind_param($record, "ii", $donor_id, $hospital_id);
                mysqli_stmt_execute($record);
                $donor_update = mysqli_prepare($conn, "UPDATE donors SET last_donation_date = CURDATE(), available = 0 WHERE id = ?");
                mysqli_stmt_bind_param($donor_update, "i", $donor_id);
                mysqli_stmt_execute($donor_update);
            }
            $message = "Appointment status updated.";
        }
    }
}

$donors = mysqli_query($conn, "SELECT d.id, u.name, d.blood_group, d.city FROM donors d INNER JOIN users u ON u.id = d.user_id WHERE u.status = 'active' AND d.available = 1 AND (d.last_donation_date IS NULL OR DATE_ADD(d.last_donation_date, INTERVAL 90 DAY) <= CURDATE()) ORDER BY u.name");
$appointments_stmt = mysqli_prepare($conn, "SELECT a.id, a.appointment_time, a.location, a.status, u.name AS donor_name, d.blood_group FROM appointments a INNER JOIN donors d ON d.id = a.donor_id INNER JOIN users u ON u.id = d.user_id WHERE a.hospital_id = ? ORDER BY a.appointment_time DESC");
mysqli_stmt_bind_param($appointments_stmt, "i", $hospital_id);
mysqli_stmt_execute($appointments_stmt);
$appointments = mysqli_stmt_get_result($appointments_stmt);
?>
<?php $page_title = "Appointments";
require "header.php"; ?><main class="role-main">
    <div class="role-container">
        <p class="role-kicker">Hospital</p>
        <h1 class="role-title">Donation Appointments</h1>
        <p class="role-subtitle">Schedule eligible donors and keep each appointment slot unique.</p><?php require "tabs.php"; ?>
        <?php if ($message !== ""): ?><p class="badge badge-fulfilled"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <?php if ($error !== ""): ?><p class="badge badge-cancelled"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <section class="role-form" style="margin-bottom:22px">
            <h2>Schedule an appointment</h2>
            <form method="post" class="role-form-grid">
                <input type="hidden" name="action" value="schedule">
                <div>
                    <label for="donor_id">Eligible donor</label>
                    <select id="donor_id" name="donor_id" required>
                        <?php while ($donor = mysqli_fetch_assoc($donors)): ?>
                            <option value="<?= (int)$donor["id"] ?>">
                                <?= htmlspecialchars($donor["name"] . " - " . $donor["blood_group"] . " (" . $donor["city"] . ")") ?>
                            </option><?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label for="appointment_time">Date and time</label>
                    <input id="appointment_time" name="appointment_time" type="datetime-local" required>
                </div>
                <div>
                    <label for="location">Extraction location</label>
                    <input id="location" name="location" value="<?= htmlspecialchars($hospital["city"] ?? "") ?>" required>
                </div>
                <button class="role-button" type="submit">Schedule</button>
            </form>
        </section>
        <section class="role-table-wrap">
            <table class="role-table">
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>When</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody><?php while ($row = mysqli_fetch_assoc($appointments)): ?><tr>
                            <td><?= htmlspecialchars($row["donor_name"] . " - " . $row["blood_group"]) ?></td>
                            <td><?= htmlspecialchars($row["appointment_time"]) ?></td>
                            <td><?= htmlspecialchars($row["location"]) ?></td>
                            <td><?= htmlspecialchars($row["status"]) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="action" value="status">
                                    <input type="hidden" name="appointment_id" value="<?= (int)$row["id"] ?>">
                                    <select name="status">
                                            <option>scheduled</option>
                                            <option>completed</option>
                                            <option>cancelled</option>
                                            <option>no-show</option>
                                    </select>
                                    <button class="role-button" type="submit">Update</button>
                                </form>
                            </td>
                        </tr><?php endwhile; ?>
                    </tbody>
            </table>
        </section>
    </div>
</main>