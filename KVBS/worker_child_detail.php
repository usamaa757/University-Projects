<?php
include("db_connect.php");
include("header.php");


$message = "";
$error = "";

$worker_id = $_SESSION['user_id'];
$parent_id = $_GET['parent_id'] ?? null;

if (!$parent_id) {
    echo "Parent ID missing!";
    exit();
}

// --- Fetch parent info ---
$parent_res = mysqli_query($conn, "SELECT full_name, email, phone FROM users WHERE id='$parent_id'");
$parent = mysqli_fetch_assoc($parent_res);

// --- Fetch all vaccines ---
$vaccine_res = mysqli_query($conn, "SELECT id, vaccine_name FROM vaccines ORDER BY vaccine_name ASC");

// --- Fetch children and their bookings ---
$sql = "SELECT b.id AS booking_id, c.id AS child_id, c.child_name, c.dob, c.gender, b.status
        FROM bookings b
        JOIN children c ON b.child_id = c.id
        WHERE b.parent_id='$parent_id' AND (b.status='confirmed' OR b.status='pending')
        ORDER BY c.child_name ASC";
$result = mysqli_query($conn, $sql);

// --- Handle marking a child as vaccinated and selecting vaccine ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_child'])) {
    $booking_id = $_POST['booking_id'];
    $vaccine_id = $_POST['vaccine_id'];

    // Fetch vaccine name
    $vaccine_row = mysqli_query($conn, "SELECT vaccine_name FROM vaccines");
    $vaccine = mysqli_fetch_assoc($vaccine_row)['vaccine_name'];

    $update = mysqli_query($conn, "UPDATE bookings SET status='completed', worker_id='$worker_id', vaccine_name='$vaccine', vaccinated_at = NOW() WHERE id='$booking_id'");
    if ($update) {
        $message = "Child vaccination marked as completed!";
        header("Refresh:2"); //reload to update table
    } else {
        $error = "Error updating status: " . mysqli_error($conn);
    }
}
?>

<div class="management-container">
    <a class="back" href="worker_dashboard.php">← Back to Dashboard</a>

    <h2>Parent: <?php echo htmlspecialchars($parent['full_name']); ?></h2>
    <p>Email: <?php echo htmlspecialchars($parent['email']); ?> | Phone:
        <?php echo htmlspecialchars($parent['phone']); ?></p>


    <h2>Children & Vaccination</h2>

    <?php
    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    } else {
        echo "<div class='message'>$message</div>";
    }
    ?>
    <table>
        <tr>
            <th>Child Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Vaccine</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['child_name']); ?></td>
            <td><?php echo htmlspecialchars($row['dob']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td>
                <?php if ($row['status'] == 'confirmed') { ?>
                <form method="POST" style="display:flex; gap:5px; align-items:center;">
                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                    <select name="vaccine_id" required>
                        <option value="">-- Select Vaccine --</option>
                        <?php
                                mysqli_data_seek($vaccine_res, 0);
                                while ($v = mysqli_fetch_assoc($vaccine_res)) {
                                    echo "<option value='{$v['id']}'>{$v['vaccine_name']}</option>";
                                }
                                ?>
                    </select>
                    <button type="submit" name="complete_child">Mark Completed</button>
                </form>
                <?php } elseif ($row['status'] == 'completed') { ?>
                <span style="color:green;">✔ <?php echo htmlspecialchars($row['vaccine_name']); ?></span>
                <?php } else { ?>
                <span style="color:gray;">Pending Approval</span>
                <?php } ?>
            </td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td>
                <?php if ($row['status'] == 'confirmed') { ?>
                <span style="color:blue;">Select vaccine & complete</span>
                <?php } elseif ($row['status'] == 'completed') { ?>
                <span style="color:green;">Done</span>
                <?php } else { ?>
                <span style="color:gray;">Pending</span>
                <?php } ?>
            </td>
        </tr>
        <?php
        }
        ?>
    </table>
</div>

<?php

include('footer.php');

?>