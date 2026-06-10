<?php
include("db_connect.php");
include("header.php");


$parent_id = $_SESSION['user_id'];
$message = "";
$error = "";

// --- Fetch all available vaccines ---
$vaccine_query = mysqli_query($conn, "SELECT * FROM vaccines ORDER BY vaccine_name ASC");

// --- Fetch all children for this parent ---
$children_query = mysqli_query($conn, "SELECT * FROM children WHERE parent_id='$parent_id' ORDER BY child_name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_ids = isset($_POST['child_id']) ? $_POST['child_id'] : [];
    $preferred_date = $_POST['preferred_date'];
    $preferred_time = $_POST['preferred_time'];

    if (empty($child_ids) || empty($preferred_date) || empty($preferred_time)) {
        $error = " All fields are required!";
    } else {
        $success_count = 0;
        $fail_count = 0;

        foreach ($child_ids as $child_id) {
            $sql = "INSERT INTO bookings (parent_id, child_id, preferred_date, preferred_time, status)
                    VALUES ('$parent_id', '$child_id', '$preferred_date', '$preferred_time', 'pending')";

            if (mysqli_query($conn, $sql)) {
                $success_count++;
            } else {
                $fail_count++;
            }
        }

        if ($success_count > 0) {
            $message = " Booking successful for $success_count child(ren)! Your requests are pending confirmation.";
        } else {
            $error = "Failed to submit bookings.";
        }
    }
}


?>

<div class="form-container">
    <a class="back" href="parent_dashboard.php">← Back to Dashboard</a>
    <h2>Book Vaccination Visit for Your Child(ren)</h2>

    <?php
    if (!empty($error)) {
        echo "<div class='error' style='color:red;text-align:center;'>$error</div>";
    } elseif (!empty($message)) {
        echo "<div class='message' style='color:green;text-align:center;'>$message</div>";
    }
    ?>

    <?php if (mysqli_num_rows($children_query) > 0) { ?>
    <form method="POST">
        <label for="child">Select Child(ren):</label>
        <select name="child_id[]" multiple required size="4" style="height:auto;">
            <?php while ($c = mysqli_fetch_assoc($children_query)) { ?>
            <option value="<?php echo $c['id']; ?>">
                <?php echo htmlspecialchars($c['child_name']) . " (" . $c['gender'] . ", " . $c['dob'] . ")"; ?>
            </option>
            <?php } ?>
        </select>
        <small style="color:gray;">(Hold CTRL or CMD to select multiple children)</small>

        <label for="preferred_date">Preferred Date:</label>
        <input type="date" name="preferred_date" required min="<?php echo date('Y-m-d'); ?>">

        <label for="preferred_time">Preferred Time:</label>
        <input type="time" name="preferred_time" required>

        <div class="text-center">
            <button type="submit">Book Visit</button>
        </div>
    </form>
    <?php } else { ?>
    <div style="text-align:center; color:red;">
        ⚠️ You need to add your child's details first before booking a vaccine.<br>
        <a href="child_list.php" style="color:#0077b6; font-weight:bold;">Add Child Now →</a>
    </div>
    <?php } ?>
</div>
<?php

include('footer.php');

?>
</body>

</html>