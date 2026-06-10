<?php
include 'header.php';
include 'db_connect.php';
$message = '';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $contact = $_POST['contact'];
    $organization = $_POST['organization'];
    $type = $_POST['type']; // Farmer / Researcher / Firm

    $sql = "INSERT INTO agents (name, email, password, contact, organization, type, status)
            VALUES ('$name', '$email', '$password', '$contact', '$organization', '$type', 'Pending')";
    if ($conn->query($sql) === TRUE) {
        $message = "Registration successful! Await admin approval.";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<div class="container">

    <h2>🌾 Seed Agent Registration</h2>
    <form method="POST">
        <p style="color:green;"><?php echo $message; ?></p>

        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Contact No:</label>
        <input type="text" name="contact" required>

        <label>Organization / Farm Name:</label>
        <input type="text" name="organization" required>

        <label>Agent Type:</label>
        <select name="type" required>
            <option value="Farmer">Farmer</option>
            <option value="Researcher">Researcher</option>
            <option value="Firm">Firm</option>
        </select>

        <button type="submit" name="register" class="btn">Register</button>
    </form>
    </body>

</div>

</html>