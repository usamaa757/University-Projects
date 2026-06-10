<?php
include 'navbar.php';

include 'db.php';


// form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role        = $_POST['role'];
    $full_name   = $_POST['full_name'];
    $employee_id = $_POST['employee_id'];
    $email       = $conn->real_escape_string($_POST['email']); // escape for safety
    $password    = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $cnic        = $_POST['cnic'];
    $qual        = $_POST['qualifications'];
    $contact     = $_POST['contact_info'];
    $center      = $_POST['center_preferences'];
    $avail       = $_POST['availability'];


    $check = $conn->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");

    if ($check && $check->num_rows > 0) {
        $error = "This email is already registered. Please use another email or login.";
    } else {
        // Insert only if email does not exist
        $sql = "INSERT INTO users 
                (role, full_name, employee_id, email, password, cnic, qualifications, contact_info, center_preferences, availability) 
                VALUES 
                ('$role','$full_name','$employee_id','$email','$password','$cnic','$qual','$contact','$center','$avail')";

        if ($conn->query($sql) === TRUE) {
            $success = "Registration successful! You can now login after admin approval.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

?>

<div class="container">
    <h2>User Registration</h2>
    <?php if (isset($success)) echo "<p class='msg success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>

    <form method="POST" action="">
        <label>Role</label>
        <select name="role" required>
            <option value="superintendent">Superintendent</option>
            <option value="invigilator">Invigilator</option>
        </select>
        <label>Full Name</label>
        <input type="text" name="full_name" placeholder="Enter your full name" required>

        <label>Employee ID</label>
        <input type="text" name="employee_id" placeholder="Enter your employee ID" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email address" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Create a strong password" required>

        <label>CNIC</label>
        <input type="text" name="cnic" placeholder="Enter your CNIC (e.g. 12345-6789012-3)" required>

        <label>Qualifications</label>
        <input type="text" name="qualifications" placeholder="Enter your qualifications">

        <label>Contact Info</label>
        <input type="text" name="contact_info" placeholder="Enter phone number or address">

        <label>Center Preferences</label>
        <input type="text" name="center_preferences" placeholder="Enter preferred exam centers">

        <label>Availability</label>
        <select name="availability" required>
            <option value="morning">Morning</option>
            <option value="afternoon">Afternoon</option>
            <option value="evening">Evening</option>
        </select>


        <button type="submit">Register</button>
    </form>
    <p style="text-align:center; margin-top:10px;">Already have an account? <a href="login.php">Login</a></p>

</div>
</body>

</html>