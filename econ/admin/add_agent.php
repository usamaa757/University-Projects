<?php
include 'header.php';
include '../db.php';
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stripe_id = $_POST['stripe_id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        $error = " Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $role = 'agent';

        // Handle image upload
        $imageName = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileTmp = $_FILES['photo']['tmp_name'];
            $fileType = mime_content_type($fileTmp);
            $fileName = basename($_FILES['photo']['name']);
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

            if (in_array($fileType, $allowedTypes)) {
                $imageName = uniqid('agent_') . '.' . $fileExt;
                $targetDir = 'uploads/agents/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                move_uploaded_file($fileTmp, $targetDir . $imageName);
            } else {
                $error = " Only JPG and PNG files are allowed.";
                $imageName = '';
            }
        }

        // Check email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = " Email already registered!";
        } else {

            // Add agent with image
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password, stripe_account_id, role, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $fullname, $email, $phone, $hashed, $stripe_id, $role, $imageName);

            if ($stmt->execute()) {
                $success = "Agent added successfully.";
            } else {
                $error = " Error: " . $stmt->error;
            }
        }

        $check->close();
    }
}

$conn->close();
?>


<main class="container">
    <section class="section">
        <div class="section-header">
            <i class="fas fa-user-tie"></i>
            <h2>Add New Agent</h2>
        </div>
        <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <form action="add_agent.php" method="POST" enctype="multipart/form-data">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required>

            <label for="stripe_id">Stripe Account ID</label>
            <input type="text" id="stripe_id" name="stripe_id" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <label for="photo">Profile Picture</label>
            <input type="file" id="photo" name="photo" accept="image/*">

            <div class="text-center">
                <button class="btn" type="submit">Add Agent</button>
            </div>
        </form>

        <p style="margin-top: 20px;">Need to manage agents? <a href="manage_agents.php"
                style="color: var(--dark); text-decoration: underline;">Go to Agent List</a>.</p>
    </section>
</main>
<?php include '../footer.php'; ?>

</body>

</html>