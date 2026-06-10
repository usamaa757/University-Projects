<?php
session_start();
include '../header.php';
include '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Determine the table to query based on role
    $table = '';
    switch ($role) {
        case 'admin':
            $table = 'admin';
            break;
        case 'doctor':
            $table = 'doctors';
            break;
        case 'patient':
            $table = 'patients';
            break;
        case 'receptionist':
            $table = 'receptionists';
            break;
        default:
            echo "<script>alert('Invalid role selected'); window.history.back();</script>";
            exit;
    }

    // Prepare and execute the query
    $sql = "SELECT * FROM $table WHERE email = ? AND status = 'accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $user['email'];

            // Redirect to dashboard based on role
            if ($role === 'admin') {
                header("Location: ../admin/admin_dashboard.php");
            } elseif ($role === 'doctor') {
                header("Location: ../doctor/doctor_dashboard.php");
            } elseif ($role === 'receptionist') {
                header("Location: ../receptionist/receptionist_dashboard.php");
            } else {
                header("Location: ../patient/patient_dashboard.php");
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No user found with this email'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">🔐 User Login</h2>
    <div class="row justify-content-center">
        <div class="col-md-6 border rounded p-4 shadow ">
            <form action="" method="POST">


                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Login as</label>
                    <select class="form-select" name="role" id="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                        <option value="patient">Patient</option>
                        <option value="receptionist">Receptionist</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>


</body>

</html>