<?php
include 'header.php';
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username         = trim($_POST['username']);
    $email            = trim($_POST['email']);
    $role             = $_POST['role'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the email already exists in either table
    $check_email_query = "SELECT email FROM organizers WHERE email = ? UNION SELECT email FROM attendees WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "<div class='error'>Email already exists. Please use a different email address.</div>";
    } else {
        if ($password !== $confirm_password) {
            $message = "<div class='error'>Password and Confirm Password do not match.</div>";
        } else {
            $hash_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into the correct table based on the role
            if ($role === 'organizer') {
                $stmt = $conn->prepare("INSERT INTO organizers (username, email, password) VALUES (?, ?, ?)");
            } elseif ($role === 'attendee') {
                $stmt = $conn->prepare("INSERT INTO attendees (username, email, password) VALUES (?, ?, ?)");
            } else {
                $message = "<div class='error'>Invalid role selected.</div>";
            }

            if (isset($stmt)) {
                $stmt->bind_param("sss", $username, $email, $hash_password);
                if ($stmt->execute()) {
                    $message = "<div class='success'>Registration successful! <a href='login.php'>Login here</a>.</div>";
                } else {
                    $message = "<div class='error'>Error: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            }
        }
    }

    $stmt->close();
    $conn->close();
}

?>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    margin: 0;
    padding: 0;
}

.register-container {
    max-width: 450px;
    margin: 60px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.register-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2c3e50;
}

.register-container label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.register-container input,
.register-container select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

.register-container input:focus,
.register-container select:focus {
    border-color: #3498db;
    outline: none;
}

.register-container button {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.register-container button:hover {
    background-color: #2980b9;
}

.success {
    color: green;
    background: #eaf9ea;
    padding: 10px;
    border: 1px solid green;
    border-radius: 5px;
    margin-bottom: 20px;
}

.error {
    color: red;
    background: #ffeaea;
    padding: 10px;
    border: 1px solid red;
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>
<div class="register-container">
    <h2>Register</h2>

    <?php if (!empty($message)) echo $message; ?>

    <form action="register.php" method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="organizer">Event Organizer</option>
            <option value="attendee">Attendee</option>
        </select>

        <button type="submit">Register</button>
    </form>
</div>
</body>

</html>