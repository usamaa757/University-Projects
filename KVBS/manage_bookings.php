<?php
include("header.php");
include("db_connect.php");

$message = "";
$error = "";

// --- Handle Approval + Assignment ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_and_assign'])) {
    $parent_id = $_POST['parent_id'];
    $preferred_date = $_POST['preferred_date'];
    $preferred_time = $_POST['preferred_time'];
    $worker_id = $_POST['worker_id'];

    if (!empty($worker_id)) {
        $query = "UPDATE bookings 
                  SET status='confirmed', worker_id='$worker_id'
                  WHERE parent_id='$parent_id' 
                  AND preferred_date='$preferred_date' 
                  AND preferred_time='$preferred_time'";
        if (mysqli_query($conn, $query)) {
            $message = "✅ All bookings for this parent/time confirmed and worker assigned!";
        } else {
            $error = "Error updating bookings: " . mysqli_error($conn);
        }
    } else {
        $error = "⚠️ Please select a worker before approving.";
    }
}

// --- Handle Rejection ---
if (isset($_GET['reject'])) {
    $parent_id = $_GET['parent_id'];
    $preferred_date = $_GET['preferred_date'];
    $preferred_time = $_GET['preferred_time'];

    $query = "UPDATE bookings 
              SET status='rejected' 
              WHERE parent_id='$parent_id' 
              AND preferred_date='$preferred_date' 
              AND preferred_time='$preferred_time'";
    if (mysqli_query($conn, $query)) {
        $message = "❌ All bookings for this parent/time rejected!";
    } else {
        $error = "Error rejecting bookings: " . mysqli_error($conn);
    }
}


// --- Fetch all grouped bookings (grouped by parent + date/time) ---
$sql = "
SELECT 
    MIN(b.id) AS booking_id,
    b.parent_id,
    u.full_name AS parent_name,
    u.email, 
    u.phone,
    GROUP_CONCAT(DISTINCT c.child_name ORDER BY c.child_name SEPARATOR ', ') AS children,
    b.preferred_date,
    b.preferred_time,
    b.status,
    w.full_name AS worker_name
FROM bookings b
JOIN users u ON b.parent_id = u.id
JOIN children c ON b.child_id = c.id
LEFT JOIN users w ON b.worker_id = w.id
GROUP BY 
    b.parent_id, 
    b.preferred_date, 
    b.preferred_time
ORDER BY 
    b.preferred_date DESC, 
    b.preferred_time DESC
";


$result = mysqli_query($conn, $sql);

// --- Fetch available workers ---
$workers = mysqli_query($conn, "SELECT id, full_name FROM users WHERE role='worker' ORDER BY full_name ASC");
?>

<div class="management-container">
    <a class="back" href="dashboard.php">← Back to Dashboard</a>

    <h2>Manage Bookings & Assign Workers</h2>

    <?php
    if (!empty($error)) {
        echo "<div class='error' style='color:red;'>$error</div>";
    } elseif (!empty($message)) {
        echo "<div class='message' style='color:green;'>$message</div>";
    }
    ?>

    <table>
        <tr>
            <th>Parent</th>
            <th>Children</th>
            <th>Contact</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Assign & Approve</th>
            <th>Reject</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['parent_name']); ?></td>

            <td><?php echo htmlspecialchars($row['children']); ?></td>


            <td>
                📞 <?php echo htmlspecialchars($row['phone']); ?><br>
                📧 <small><?php echo htmlspecialchars($row['email']); ?></small>
            </td>

            <td><?php echo htmlspecialchars($row['preferred_date']); ?></td>
            <td><?php echo htmlspecialchars($row['preferred_time']); ?></td>
            <td class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>

            <td>
                <?php if ($row['status'] == 'pending') { ?>
                <form method="POST" class="approval-form">
                    <input type="hidden" name="parent_id" value="<?php echo $row['parent_id']; ?>">
                    <input type="hidden" name="preferred_date" value="<?php echo $row['preferred_date']; ?>">
                    <input type="hidden" name="preferred_time" value="<?php echo $row['preferred_time']; ?>">
                    <select name="worker_id" required>
                        <option value="">--Select Worker--</option>
                        <?php
                                mysqli_data_seek($workers, 0);
                                while ($w = mysqli_fetch_assoc($workers)) {
                                    echo "<option value='{$w['id']}'>{$w['full_name']}</option>";
                                }
                                ?>
                    </select>
                    <button type="submit" name="approve_and_assign" class="approve">Approve & Assign All</button>
                </form>

                <a class="action reject"
                    href="?reject=1&parent_id=<?php echo $row['parent_id']; ?>&preferred_date=<?php echo $row['preferred_date']; ?>&preferred_time=<?php echo $row['preferred_time']; ?>"
                    onclick="return confirm('Reject all bookings for this parent and time?');">Reject All</a>

                <?php } else {
                        echo $row['worker_name'] ? htmlspecialchars($row['worker_name']) : "<i>Not assigned</i>";
                    } ?>
            </td>

            <td>
                <?php if ($row['status'] == 'pending') { ?>
                <a class="action reject" href="?reject=<?php echo $row['booking_id']; ?>"
                    onclick="return confirm('Reject this booking?');">Reject</a>
                <?php } else { ?>
                <span style="color:gray;">No Action</span>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php

include('footer.php');

?>