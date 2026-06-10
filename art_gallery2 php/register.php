<?php include 'db.php';
include 'header.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $confirm_password = $_POST['confirm_password'];
    if ($_POST['password'] != $confirm_password) {
        echo "<script>alert('Password does not match'); window.location.href='register.php';</script>";
        exit;
    }
    $sql = "SELECT * FROM users WHERE email = ? OR phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('Email or Phone already exists'); window.location.href='register.php';</script>";
        exit;
    }
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, email, password, role, address, phone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $email, $password, $role, $address, $phone);;

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful'); window.location.href='login.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<div class="container mt-5 ">
    <div class="row justify-content-center">
        <div class="col-md-6 card shadow">
            <h3 class="text-center">Register</h3>
            <form method="POST" class=" p-4 ">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Enter username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="Enter email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" required
                        placeholder="Enter address (it will use as shipping address)">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" maxlength="11" name="phone" class="form-control" required
                        placeholder="Enter phone number">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter password">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required
                        placeholder="Enter password">
                </div>
                <div class=" mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="seller">Seller</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
        </div>
    </div>
</div>