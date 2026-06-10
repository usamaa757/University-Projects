<?php
include("db_connect.php");
include("header.php");
// Redirect if not logged in or not a worker
if (!isset($_SESSION['user_id']) && $_SESSION['role'] != 'worker') {
    header("Location: login.php");
    exit();
}
$worker_id = $_SESSION['user_id'];
$message = "";
$error = "";

// --- Fetch assigned bookings grouped by parent + date/time ---
$sql = "
SELECT 
    b.parent_id,
    u.full_name AS parent_name,
    u.email, u.phone, u.address,
    GROUP_CONCAT(c.child_name SEPARATOR ', ') AS children,
    b.preferred_date,
    b.preferred_time,
    MAX(b.status) AS status
FROM bookings b
JOIN users u ON b.parent_id = u.id
JOIN children c ON b.child_id = c.id
WHERE b.worker_id='$worker_id'
GROUP BY b.parent_id, b.preferred_date, b.preferred_time
ORDER BY b.preferred_date ASC, b.preferred_time ASC
";

$result = mysqli_query($conn, $sql);
?>

<div class="management-container">

    <h2> My Assigned Vaccination Visits</h2>

    <?php

    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    } else {
        echo "<div class='message'>$message</div>";
    }
    ?>

    <table>
        <tr>
            <th>Parent</th>
            <th>Children</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td>
                <strong><?php echo htmlspecialchars($row['parent_name']); ?></strong><br>
                📞 <?php echo htmlspecialchars($row['phone']); ?><br>
                📧 <?php echo htmlspecialchars($row['email']); ?><br>
                🏠 <?php echo htmlspecialchars($row['address']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['children']); ?>
            </td>

            <td><?php echo htmlspecialchars($row['preferred_date']); ?></td>
            <td><?php echo htmlspecialchars($row['preferred_time']); ?></td>
            <td class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>

            <td>
                <?php if ($row['status'] == 'confirmed') { ?>
                <a class="action confirmed" href="worker_child_detail.php?parent_id=<?php echo $row['parent_id']; ?>">
                    View & Vaccinate
                </a>
                <?php } elseif ($row['status'] == 'completed') { ?>
                <span style="color:green;">✔ Done</span>
                <?php } else { ?>
                <span style="color:gray;">Pending Approval</span>
                <?php } ?>
            </td>

        </tr>
        <?php }
        } else { ?>
        <tr>
            <td colspan="6" style="text-align:center;">No bookings assigned yet.</td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php

include('footer.php');

?>