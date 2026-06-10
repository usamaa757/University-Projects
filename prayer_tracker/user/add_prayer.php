<?php
include 'header.php';
include '../db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$today_date = date('Y-m-d'); // Get today's date
$user_id = $_SESSION['user_id'];

// Fetch all prayer names from the 'prayer' table
$query = "SELECT prayer_id, prayer_name FROM prayer";
$result = $conn->query($query);

// Check if there are any rows in the 'prayer' table
if ($result->num_rows > 0) {
    // Fetch all the prayer prayers and store them in an array
    $prayer_options = [];
    while ($row = $result->fetch_assoc()) {
        $prayer_options[] = $row;
    }
} else {
    // If no data found, initialize prayer_options as an empty array
    $prayer_options = [];
}

// Check if the user has already updated the status for today
$query = "SELECT * FROM prayer_records WHERE user_id = ? AND date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $today_date);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

$message = ""; // Initialize the message variable

if (isset($_POST['submit'])) {
    // Capture the input data from the form
    $status = $_POST['status'];
    $prayer = isset($_POST['prayer']) ? $_POST['prayer'] : [];

    // Loop through the prayer array and insert/update individual records for each prayer
    foreach ($prayer as $prayer_item) {
        // Prepare the SQL query to check if there's already a record for today for this prayer_id
        $query = "SELECT * FROM prayer_records WHERE user_id = ? AND date = ? AND prayer_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $user_id, $today_date, $prayer_item);
        $stmt->execute();
        $result = $stmt->get_result();

        // If a record exists, update it
        if ($result->num_rows > 0) {
            $update_query = "UPDATE prayer_records SET status = ? WHERE user_id = ? AND date = ? AND prayer_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sisi", $status, $user_id, $today_date, $prayer_item);
            if ($stmt->execute()) {
                echo "<script>alert('Record updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating record: " . addslashes($conn->error) . "');</script>";
            }
        } else {
            // If no record exists, insert a new one
            $insert_query = "INSERT INTO prayer_records (user_id, date, prayer_id, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isis", $user_id, $today_date, $prayer_item, $status);
            if ($stmt->execute()) {
                echo "<script>alert('Record added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding record: " . addslashes($conn->error) . "');</script>";
            }
        }
    }

    // Close the statement
    $stmt->close();
}

$conn->close();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h4>Upate Prayer</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <!-- prayer Selection with Multiple Selection -->
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Prayer</label>
                                <select name="prayer[]" class="form-select" multiple required>
                                    <?php
                                    // Loop through the prayer options and display them as select options
                                    foreach ($prayer_options as $prayer) {
                                        echo "<option value='" . $prayer['prayer_id'] . "'>" . ucfirst($prayer['prayer_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Status Selection for prayer -->
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="completed">Completed</option>
                                    <option value="qaza">Qaza</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-center">

                            <button type="submit" name="submit" class="btn btn-dark">Update Status</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>


</body>

</html>