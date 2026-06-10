<?php include 'navbar.php';


require "db.php";

$msg = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role = $_POST['role'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $student_id = $_POST['student_id'] ?? '';
    $program = $_POST['program'] ?? '';
    $password = $_POST['password'];

    // ---------- VALIDATION ----------
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email format.";
    } elseif (!preg_match("/@vu\.edu(\.pk)?$/", $email)) {
        $error = "You must register with your university email address.";
    } elseif (!in_array($role, ['student', 'faculty'])) {
        $msg = "Invalid role selected.";
    } elseif (empty($password)) {
        $msg = "Password cannot be empty.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Escape inputs
        $role = mysqli_real_escape_string($conn, $role);
        $full_name = mysqli_real_escape_string($conn, $full_name);
        $email = mysqli_real_escape_string($conn, $email);
        $student_id = mysqli_real_escape_string($conn, $student_id);
        $program = mysqli_real_escape_string($conn, $program);
        $password_hash = mysqli_real_escape_string($conn, $password_hash);

        // If role is faculty, clear student-only fields
        if ($role == "faculty") {
            $student_id = "";
            $program = "";
        }

        // ---------- INSERT QUERY ----------
        $sql = "INSERT INTO users (role, full_name, university_email, password_hash, student_id, program, is_active)
                VALUES ('$role', '$full_name', '$email', '$password_hash', '$student_id', '$program', 0)";

        if (mysqli_query($conn, $sql)) {
            $msg = "Registration successful! Await admin approval.";
        } else {
            if (mysqli_errno($conn) == 1062) {
                $error = "Error: Email already registered.";
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }

    mysqli_close($conn);
}
?>


<!-- REGISTRATION FORM -->
<div class="container">
    <h2>User Registration</h2>
    <?php if (!empty($msg)) echo '<p class="msg">' . $msg . '</p>'; ?>
    <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>

    <?php if (!empty($error)) { ?>
    <p style="color: red; text-align:center;"><?php echo $error; ?></p>
    <?php } ?>

    <form action="register.php" method="POST">

        <label for="role">Register As</label>
        <select name="role" id="role" required>
            <option value="">-- Select Role --</option>
            <option value="student">Student</option>
            <option value="faculty">Faculty / Supervisor</option>
        </select>

        <label for="name">Full Name</label>
        <input type="text" id="name" name="full_name" placeholder="Enter your full name" required>

        <label for="email">University Email</label>
        <input type="email" id="email" name="email" placeholder="example@vu.edu.pk" required>

        <label for="studentid">Student ID (if applicable)</label>
        <input type="text" id="studentid" name="student_id" placeholder="Enter Student ID (optional)">

        <label for="program">Study Program (for students)</label>
        <input type="text" id="program" name="program" placeholder="e.g. BS Computer Science">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Register</button>
    </form>

    <p class="note">Already have an account? <a href="login.php">Login</a></p>
</div>

</body>

</html>