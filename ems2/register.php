<?php

include 'header.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];
    $confirm_password     = $_POST['confirm_password'];
    $password     = $_POST['password'];
    if ($password != $confirm_password) {
        echo "<script>alert('Password and Confirm Password do not match');window.location.href='register.php';</script>";
        exit;
    } else {
        $hash_password = password_hash($password, PASSWORD_DEFAULT);


        if ($role === 'organizer') {
            $stmt = $conn->prepare("INSERT INTO organizers (username, email, password) VALUES (?, ?, ?)");
        } elseif ($role === 'attendee') {
            $stmt = $conn->prepare("INSERT INTO attendees (username, email, password) VALUES (?, ?, ?)");
        } else {
            echo "<script>alert('Invalid role selected');</script>";
            exit;
        }

        $stmt->bind_param("sss", $username, $email, $hash_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    }
    $stmt->close();
    $conn->close();
}
?>




<!-- Registration Form -->
<div class="container rounded shadow border col-md-5 mx-auto mt-5">
    <div class="p-3">
        <h2 class="mb-4 text-center">Register</h2>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" required class="form-control">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" required class="form-control">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" required class="form-control">
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required class="form-control">
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="organizer">Event Organizer</option>
                    <option value="attendee">Attendee</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>