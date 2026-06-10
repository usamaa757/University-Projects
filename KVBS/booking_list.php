<?php
include("db_connect.php");
include("header.php");


$parent_id = $_SESSION['user_id'];
$message = "";
$error = "";

// --- Handle Delete Booking ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $check = mysqli_query($conn, "SELECT * FROM bookings WHERE id='$id' AND parent_id='$parent_id' AND status='pending'");
    if (mysqli_num_rows($check) > 0) {
        $delete = mysqli_query($conn, "DELETE FROM bookings WHERE id='$id'");
        if ($delete) {
            $message = "Booking cancelled successfully.";
        } else {
            $error = "Error cancelling booking: " . mysqli_error($conn);
        }
    } else {
        $error = "You can only cancel pending bookings!";
    }
}

// --- Fetch User Bookings with Worker Info ---
$query = "
    SELECT 
        b.*, 
        w.full_name AS worker_name, 
        w.email AS worker_email, 
        w.phone AS worker_phone
    FROM bookings b
    LEFT JOIN users w ON b.worker_id = w.id
    WHERE b.parent_id = '$parent_id'
    ORDER BY b.id DESC
";
$result = mysqli_query($conn, $query);
?>

<div class="management-container">
    <a class="back" href="parent_dashboard.php">← Back to Dashboard</a>

    <?php
    if (!empty($message)) echo "<div class='message' style='color:green;'>$message</div>";
    if (!empty($error)) echo "<div class='error' style='color:red;'>$error</div>";
    ?>

    <h2>My Vaccination Bookings</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Vaccine</th>
            <th>Preferred Date</th>
            <th>Preferred Time</th>
            <th>Status</th>
            <th>Assigned Worker</th>
            <th>Action</th>
        </tr>

        <?php if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['vaccine_name']); ?></td>
            <td><?php echo htmlspecialchars($row['preferred_date']); ?></td>
            <td><?php echo htmlspecialchars($row['preferred_time']); ?></td>
            <td class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>

            <td>
                <?php
                        if (!empty($row['worker_name'])) {
                            echo "<strong>" . htmlspecialchars($row['worker_name']) . "</strong><br>";
                            echo "<small>📞 " . htmlspecialchars($row['worker_phone']) . "</small><br>";
                            echo "<small>✉️ " . htmlspecialchars($row['worker_email']) . "</small>";
                        } else {
                            echo "<i>Not Assigned</i>";
                        }
                        ?>
            </td>

            <td>
                <?php if ($row['status'] == 'pending') { ?>
                <a class="action edit" href="update_booking.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="action delete" href="?delete=<?php echo $row['id']; ?>"
                    onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
                <?php } else { ?>
                <span style="color:gray;">No Action</span>
                <?php } ?>
            </td>
        </tr>
        <?php }
        } else { ?>
        <tr>
            <td colspan="7" style="text-align:center;">No bookings found.</td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php

include('footer.php');

?>
</body>

</html>