<?php
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Choose the table based on role
    if ($role === 'seeker') {
        $query = "SELECT seeker_id AS id, name, email, password_hash FROM job_seekers WHERE email = '$email'";
    } elseif ($role === 'employer') {
        $query = "SELECT employer_id AS id, name, email, password_hash FROM employers WHERE email = '$email'";
    } else {
        echo "<script>alert('Invalid role selected.');</script>";
        exit;
    }

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $name = $row['name'];
        $fetched_email = $row['email'];
        $hashed_password = $row['password_hash'];

        if (password_verify($password, $hashed_password)) {
            // Login successful, set session
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $fetched_email;
            $_SESSION['user_role'] = $role;

            if ($role === 'seeker') {
                header("Location: job_seeker/dashboard.php");
            } elseif ($role === 'employer') {
                header("Location: employer/dashboard.php");
            }
            exit;
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('Email not found.');</script>";
    }

    mysqli_close($conn);
}
?>


<div class="form-container">


    <form method="post" class="forms">
        <h2>Login</h2>

        <label>Email Address</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>
        <label>Role</label>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="seeker">Job Seeker</option>
            <option value="employer">Employer</option>
        </select>
        <div class="text-center">

            <button class="btn" type="submit">Login</button>
            <a href="forgot_password.php" class="btn">Forgot Password</a>
        </div>
    </form>
</div>
</body>

</html>