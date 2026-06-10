<?php
include 'header.php';
include "db.php";
session_start();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check in customers table
    $sql_customer = "SELECT * FROM customer WHERE email = ?";
    $stmt = $conn->prepare($sql_customer);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_customer = $stmt->get_result();

    // Check in owners table
    $sql_owner = "SELECT * FROM owner WHERE email = ?";
    $stmt = $conn->prepare($sql_owner);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_owner = $stmt->get_result();

    // Determine if the customer is found in customers or owners
    if ($result_customer->num_rows > 0) {
        $row = $result_customer->fetch_assoc();
    } elseif ($result_owner->num_rows > 0) {
        $row = $result_owner->fetch_assoc();
    } else {
        echo "<script>alert('No customer found with this email!');window.location.href = 'login.php';
</script>";

        exit();
    }

    // Verify password
    if (password_verify($password, $row['password'])) {
        $_SESSION['customer_id'] =  $row['customer_id'];
        $_SESSION['owner_id'] =  $row['owner_id'];
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $row['name'];
        if ($_SESSION['owner_id']) {
            header("Location: owner/dashboard.php");
            exit();
        } else {
            // Redirect to dashboard
            header("Location: customer/dashboard.php");
            exit();
        }
    } else {
        echo "<script>alert('Invalid password!');window.location.href = 'login.php';
</script>";
        exit();
    }
}
?>


<div class="form-container">
    <h2>Login</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="btn-login" type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>

</html>