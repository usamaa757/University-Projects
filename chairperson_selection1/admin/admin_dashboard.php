<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details from the database
include '../db_connection.php';
$query = "SELECT * FROM admins WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();
$conn->close();
include "header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <h4 class="card-title text-center mb-0">Welcome, <?php echo htmlspecialchars($admin['name']); ?></h4>
                </div>
                <div class="card-body">
                    <p>Admin ID: <?php echo htmlspecialchars($admin['admin_id']); ?></p>

                    <p>Email: <?php echo htmlspecialchars($admin['email']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>