<?php
include 'header.php';

include 'db.php'; // adjust path if needed

$success = "";
$error = "";

// 1. If user just enters email to check their messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_email'])) {
    $_SESSION['email'] = trim($_POST['check_email']);
}

// 2. If user submits a new message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fullname'], $_POST['email'], $_POST['message'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($fullname && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (fullname, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $message);
        $stmt->execute();

        $_SESSION['email'] = $email; // Save email to session
        $success = "Your message has been sent successfully!";
    } else {
        $error = "Please fill in all fields.";
    }
}

// 3. Fetch the latest message & reply using session email
$last_reply = null;
if (!empty($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT message, reply FROM contact_messages WHERE email = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $last_reply = $stmt->get_result()->fetch_assoc();
}
?>




<div class="contact-container">
    <section class="section">
        <div class="section-header">
            <i class="fas fa-contact"></i>
            <h2>Contact & Support</h2>
        </div>
        <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <!-- Email-only form to check previous message -->
        <form method="POST" style="margin-bottom: 20px;">
            <label for="check_email">Enter your email to view your last message and reply:</label><br>
            <input type="email" name="check_email" id="check_email" required
                value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" />
            <div class="text-center">

                <button type="submit" class="btn">Check</button>
            </div>
        </form>

        <form action="" method="POST">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
            <label for="message">Message</label>
            <textarea id="message" rows="5" name="message" placeholder="Describe your issue or question..."
                required></textarea>
            <div class="text-center">

                <button type="submit" class="btn">Send Message</button>
            </div>
        </form>
        <br>
        <?php if (!empty($last_reply)): ?>
        <div style="border: 1px solid #ccc; border-radius: 8px; padding: 15px;">
            <h4>Your Last Message:</h4>
            <p><?= nl2br(htmlspecialchars($last_reply['message'])) ?></p>

            <?php if (!empty($last_reply['reply'])): ?>
            <h4>Admin Reply:</h4>
            <p style="color: green;"><?= nl2br(htmlspecialchars($last_reply['reply'])) ?></p>
            <?php else: ?>
            <p style="color: gray;">No reply from admin yet.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>

    <div class="section">
        <h3>Terms & Policies</h3>
        <div class="policy-links">
            <a href="terms_policy.php" target="_blank">View Terms of Service</a>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

</body>

</html>