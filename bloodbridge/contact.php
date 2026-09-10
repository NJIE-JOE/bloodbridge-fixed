<?php
/*
 * FILE: contact.php
 * PURPOSE: Public contact page. It displays contact/help information for people using the system.
 * NOTE: The executable code below is unchanged; comments are added to explain it.
 */
// Load a shared file needed by this page.
require "includes/config.php";

$sent_ok = false;
// Handle form submission when the user sends a POST request.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $message = trim($_POST['message']);
    if ($name != "" && $message != "") {
        $sent_ok = true;
    }
}

$hospitals = array();
// Run a MySQL query and store the returned result.
$result = mysqli_query($conn, "SELECT hospitals.* FROM hospitals
                                JOIN users ON users.id = hospitals.user_id
                                WHERE users.status = 'active'");
// Loop through all rows returned by the database query.
while ($row = mysqli_fetch_assoc($result)) {
    $hospitals[] = $row;
}

// Set the title used by the shared page header.
$page_title = "Donation Centers";
// Load a shared file needed by this page.
require "includes/public_header.php";
?>

<!-- Page section: this block creates one visual/content section of the interface. -->
<section class="section">
  <div class="container">
    <p class="label-eyebrow">Find a Center</p>
    <h1 class="h1-display">Donation centers near you</h1>

    <div class="grid-3" style="margin-top: 28px;">
      <?php foreach ($hospitals as $hospital) { ?>
        <div class="card card-hover-lift">
          <div class="icon-circle icon-circle--soft-teal">🏥</div>
          <h4 class="h4-sub-label"><?php echo safe($hospital['name']); ?></h4>
          <p class="label-small" style="margin-top:4px;"><?php echo safe($hospital['address']); ?>, <?php echo safe($hospital['city']); ?></p>
        </div>
      <?php } ?>
      <?php if (count($hospitals) == 0) { ?>
        <p class="body-default">No verified centers yet - check back soon.</p>
      <?php } ?>
    </div>

    <div class="card" style="max-width: 520px; margin-top: 56px;">
      <h3 class="h3-card-title">Have a question?</h3>

      <?php if ($sent_ok) { ?>
        <div class="alert alert-success" style="margin-top:16px;">Thanks! We'll get back to you soon.</div>
      <?php } else { ?>
<!-- Form block: collects user input and sends it to the page for processing. -->
        <form method="post" style="display:flex; flex-direction:column; gap:16px; margin-top:16px;">
          <div class="field">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" required>
          </div>
          <div class="field">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      <?php } ?>
    </div>
  </div>
</section>

<?php require "includes/public_footer.php"; ?>
