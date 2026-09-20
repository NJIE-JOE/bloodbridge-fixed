<?php
$page_title = $page_title ?? "Hospital";
$hospital_name = "";
if (isset($conn, $_SESSION["user_id"])) {
    $q = mysqli_prepare($conn, "SELECT name FROM hospitals WHERE user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($q, "i", $_SESSION["user_id"]);
    mysqli_stmt_execute($q);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    $hospital_name = $row["name"] ?? "";
}
$hospital_initial = $hospital_name !== "" ? strtoupper(substr($hospital_name, 0, 1)) : "";
$notifications = [];
if ($hospital_name !== "" && isset($conn)) {
    $q = mysqli_prepare($conn, "SELECT id FROM hospitals WHERE user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($q, "i", $_SESSION["user_id"]);
    mysqli_stmt_execute($q);
    $hid = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($q))["id"] ?? 0);
    if ($hid) {
        $q = mysqli_prepare($conn, "SELECT a.appointment_time,u.name AS donor_name FROM appointments a INNER JOIN donors d ON d.id=a.donor_id INNER JOIN users u ON u.id=d.user_id WHERE a.hospital_id=? AND a.appointment_time>=NOW() AND a.status='scheduled' ORDER BY a.appointment_time");
        mysqli_stmt_bind_param($q, "i", $hid);
        mysqli_stmt_execute($q);
        $rows = mysqli_stmt_get_result($q);
        while ($n = mysqli_fetch_assoc($rows)) $notifications[] = ["title" => "Upcoming appointment", "detail" => $n["donor_name"] . " · " . date("M j, Y g:i A", strtotime($n["appointment_time"])), "icon" => "calendar", "link" => "appointments.php"];
        $q = mysqli_prepare($conn, "SELECT blood_group,units,urgency FROM requests WHERE hospital_id=? AND status IN ('pending','matched') ORDER BY created_at DESC");
        mysqli_stmt_bind_param($q, "i", $hid);
        mysqli_stmt_execute($q);
        $rows = mysqli_stmt_get_result($q);
        while ($n = mysqli_fetch_assoc($rows)) $notifications[] = ["title" => "Request awaiting fulfillment", "detail" => $n["blood_group"] . " · " . (int)$n["units"] . " unit(s) · " . $n["urgency"], "icon" => "bolt", "link" => "appointments.php#requests"];
        $q = mysqli_prepare($conn, "SELECT blood_group,units FROM inventory WHERE hospital_id=? AND units<=5 ORDER BY units,blood_group");
        mysqli_stmt_bind_param($q, "i", $hid);
        mysqli_stmt_execute($q);
        $rows = mysqli_stmt_get_result($q);
        while ($n = mysqli_fetch_assoc($rows)) $notifications[] = ["title" => "Low blood stock", "detail" => $n["blood_group"] . " · " . (int)$n["units"] . " units remaining", "icon" => "triangle-exclamation", "link" => "inventory.php"];
    }
}
$count = count($notifications);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | BloodBridge</title>
    <link rel="stylesheet" href="../@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="hospital.css">
</head>

<body class="hospital-shell hospital-dashboard">
    <header class="role-header">
        <div class="role-header__inner"><a class="brand" href="dashboard.php"><i class="fa-solid fa-droplet"></i><span>BloodBridge</span></a>
            <nav class="role-header__nav">
                <div class="notification-menu"><button class="hospital-notification" id="notificationButton" type="button" aria-expanded="false"><i class="fa-regular fa-bell"></i><?php if ($count): ?><span class="notification-count"><?= $count ?></span><?php endif; ?></button>
                    <div class="notification-panel" id="notificationPanel" hidden>
                        <div class="notification-panel__header"><strong>Notifications</strong><span><?= $count ?></span></div><?php if (!$count): ?><p class="notification-empty">No new notifications.</p><?php else: foreach ($notifications as $n): ?><a class="notification-item" href="<?= htmlspecialchars($n["link"]) ?>"><span class="notification-item__icon"><i class="fa-solid fa-<?= htmlspecialchars($n["icon"]) ?>"></i></span><span><strong><?= htmlspecialchars($n["title"]) ?></strong><small><?= htmlspecialchars($n["detail"]) ?></small></span></a><?php endforeach;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                endif; ?>
                    </div>
                </div><span class="role-user"><span class="role-avatar"><?= htmlspecialchars($hospital_initial) ?></span><?= htmlspecialchars($hospital_name) ?></span><a class="role-logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
            </nav>
        </div>
    </header>
    <script>
        const b = document.getElementById('notificationButton'),
            p = document.getElementById('notificationPanel');
        if (b && p) {
            b.onclick = () => {
                const o = b.getAttribute('aria-expanded') === 'true';
                b.setAttribute('aria-expanded', String(!o));
                p.hidden = o
            };
            document.addEventListener('click', e => {
                if (!e.target.closest('.notification-menu')) {
                    b.setAttribute('aria-expanded', 'false');
                    p.hidden = true
                }
            })
        }
    </script>