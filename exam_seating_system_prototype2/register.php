<?php
include 'db.php';

include 'header.php';
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_email = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email'");

    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$password')");
        if ($insert) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error in registration!');</script>";
        }
    }
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Register</h2>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-dark w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>


</body>

</html>