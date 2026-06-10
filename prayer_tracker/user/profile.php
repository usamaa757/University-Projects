<?php
include 'header.php';
include '../db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['name'] ?? 'User';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card p-4 mb-4 border rounded shadow">
                <h2 class="mb-3 text-center">👤 My Profile</h2>
                <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
                <!-- Optional: <p><strong>Email:</strong> ...</p> -->
            </div>

            <!-- Reset Buttons -->
            <div class="card p-4 mb-4 text-center  border rounded shadow">
                <h4 class="mb-3 text-center">🧹 Reset Prayer Progress</h4>
                <form method="post" action="reset_progress.php">

                    <button type="submit" name="reset_today" class="btn btn-warning btn-sm">
                        Reset Today's Prayers
                    </button>
                    <button type="submit" name="reset_qaza" class="btn btn-danger btn-sm">
                        Reset All Qaza Prayers
                    </button>

                </form>
            </div>

            <!-- Password Update -->
            <div class="card p-4 text-center  border rounded shadow">
                <h4 class="mb-3">🔒 Change Password</h4>
                <form method="post" action="update_password.php">
                    <div class="mb-3">
                        <input type="password" name="new_password" class="form-control" placeholder="New Password"
                            required>
                    </div>


                    <button type="submit" class="btn btn-dark">Update Password</button>

                </form>
            </div>

        </div>
    </div>
</div>

</body>

</html>