<?php
session_start();
include 'header.php';

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Try to find user in organizers table
    $stmt = $conn->prepare("SELECT * FROM organizers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $org_result = $stmt->get_result();

    if ($org_result->num_rows === 1) {
        $org = $org_result->fetch_assoc();
        if (password_verify($password, $org['password'])) {
            $_SESSION['user_id'] = $org['organizer_id'];
            $_SESSION['username'] = $org['username'];
            $_SESSION['role'] = 'organizer';
            header("Location:organizer/dashboard.php");
            exit;
        }
    }

    // If not organizer, check attendees
    $stmt = $conn->prepare("SELECT * FROM attendees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $att_result = $stmt->get_result();

    if ($att_result->num_rows === 1) {
        $att = $att_result->fetch_assoc();
        if (password_verify($password, $att['password'])) {
            $_SESSION['user_id'] = $att['attendee_id'];
            $_SESSION['username'] = $att['username'];
            $_SESSION['role'] = 'attendee';
            header("Location:attendee/dashboard.php");
            exit;
        }
    }

    echo "<script>alert('Invalid email or password.'); window.history.back();</script>";
    $stmt->close();
    $conn->close();
}
?>



<!-- Login Form -->
<div class="container rounded shadow border col-md-5 mx-auto mt-5">
    <div class="p-3">
        <h2 class="mb-4 text-center">Login</h2>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" required class="form-control">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" required class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>