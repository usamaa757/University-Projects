<?php
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $second_name = $_POST['second_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];
    $town = $_POST['town'];
    $region = $_POST['region'];
    $postcode = $_POST['postcode_zip'];
    $country = $_POST['country'];
    $role = $_POST['role'];


    if ($role == 'user') {
        $sql = "INSERT INTO users (first_name, second_name, email, password, phone_number, address, town, region, postcode, country)
                VALUES ('$first_name', '$second_name', '$email', '$password', '$phone', '$address', '$town', '$region', '$postcode', '$country')";
    } else {
        $sql = "INSERT INTO admin (first_name, second_name, email, password, phone_number, address, town, region, postcode, country)
                VALUES ('$first_name', '$second_name', '$email', '$password', '$phone', '$address', '$town', '$region', '$postcode', '$country')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration Successful!'); window.location.href='register.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


<div class="register-container">
    <h2>Create an Account</h2>
    <form action="register.php" method="POST">

        <label for="first_name">First Name <span class="required">*</span></label>
        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>

        <label for="second_name">Second Name <span class="required">*</span></label>
        <input type="text" id="second_name" name="second_name" placeholder="Enter your second name" required>



        <label for="email">Email Address <span class="required">*</span></label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password <span class="required">*</span></label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password"
            required>

        <label for="phone_number">Phone Number <span class="required">*</span></label>
        <input type="text" id="phone_number" name="phone_number" placeholder="Enter your phone number" required>

        <label for="address">Address <span class="required">*</span></label>
        <input type="text" id="address" name="address" placeholder="Enter your address" required>

        <label for="town">Town <span class="required">*</span></label>
        <input type="text" id="town" name="town" placeholder="Enter your town" required>

        <label for="region">Region <span class="required">*</span></label>
        <input type="text" id="region" name="region" placeholder="Enter your region" required>

        <label for="postcode_zip">Postcode/ZIP <span class="required">*</span></label>
        <input type="text" id="postcode_zip" name="postcode_zip" placeholder="Enter your postcode/ZIP" required>

        <label for="country">Country <span class="required">*</span></label>
        <input type="text" id="country" name="country" placeholder="Enter your country" required>

        <label for="role">Select Role:</label>
        <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>

        <button type="submit" class="btn">Register</button>

        <button type="reset" class="btn">Reset</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>

</html>