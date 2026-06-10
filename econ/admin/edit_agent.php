<?php
include 'header.php';
include '../db.php';

$id = (int)$_GET['id'];
$success = "";
$error = "";
// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, phone = ? WHERE id = ? AND role = 'agent'");
    $stmt->bind_param("sssi", $fullname, $email, $phone, $id);
    if ($stmt->execute()) {
        $success = "Agent updated successfully.";
    } else {
        $error = " Update failed!";
    }
    $stmt->close();
}

// Fetch current agent data
$stmt = $conn->prepare("SELECT fullname, email, phone FROM users WHERE id = ? AND role = 'agent'");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($fullname, $email, $phone);
$stmt->fetch();
$stmt->close();
?>

<main class="container">
    <section class="section">
        <div class="section-header">
            <i class="fas fa-edit"></i>
            <h2>Edit Agent</h2>
        </div>
        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($fullname) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required>

            <button class="btn" type="submit">Update Agent</button>
            <a href="manage_agents.php" class="btn secondary">Back</a>
        </form>
    </section>
</main><?php include '../footer.php'; ?>