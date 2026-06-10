<?php include 'db.php';
include 'header.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();


    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['address'] = $user['address'];
        $_SESSION['email'] = $user['email'];

        if ($user['role'] == 'admin') {
            header("Location: admin/admin_dashboard.php");
        } elseif ($user['role'] == 'seller') {
            header("Location: seller/seller_dashboard.php");
        } elseif ($user['role'] == 'customer') {

            header("Location: customer/customer_dashboard.php");
        }
    } else {
        echo "<script>alert('Invalid Credentials');</script>";
    }
} ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 shadow card">
            <h3 class="text-center">Login</h3>
            <form method="POST" class="p-4">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>