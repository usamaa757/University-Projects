<?php
include 'navbar.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied!");
}

$user_id = $_SESSION['user_id'];

// ✅ Handle Availability Update
if (isset($_POST['update_availability'])) {
    $availability = 'leave';
    $sql = "UPDATE users SET availability = '$availability' WHERE id = $user_id";
    if ($conn->query($sql)) {
        $msg = "Availability updated successfully!";
    } else {
        $error = "Error updating availability: " . $conn->error;
    }
}

// ✅ Handle Leave Application
if (isset($_POST['apply_leave'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $conn->real_escape_string($_POST['reason']);

    $sql = "INSERT INTO leaves (user_id, start_date, end_date, reason)
            VALUES ($user_id, '$start_date', '$end_date', '$reason')";
    if ($conn->query($sql)) {
        $msg = "Leave application submitted successfully!";
    } else {
        $error = "Error applying for leave: " . $conn->error;
    }
}

// ✅ Fetch Leave History
$leaves = $conn->query("SELECT * FROM leaves WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<div class="container">

    <h2>Apply for Leave</h2>


    <!-- Leave Form -->
    <form method="post" action="apply_leave.php">

        <label>Availability:</label>
        <input type="text" name="leave" required value="Leave" disabled>

        <label>Start Date:</label>
        <input type="date" name="start_date" required>

        <label>End Date:</label>
        <input type="date" name="end_date" required>

        <label>Reason:</label>
        <textarea name="reason" required></textarea>

        <button type="submit" name="apply_leave">Apply for Leave</button>
    </form>

</div>

<div class="table-container">

    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>

    <!-- (Insert HTML forms from above here) -->

    <h3 style="margin-top:30px;">Your Leaves Record</h3>
    <table>
        <tr>
            <th>Start</th>
            <th>End</th>
            <th>Reason</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $leaves->fetch_assoc()): ?>
            <tr>
                <td><?= $row['start_date'] ?></td>
                <td><?= $row['end_date'] ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>