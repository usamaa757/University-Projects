<?php
include 'header.php';
include '../other/db_connection.php';

// Fetch classes
$class_sql = "SELECT class_id, class_name FROM classes";
$class_result = $conn->query($class_sql);
$classes = [];
if ($class_result->num_rows > 0) {
    while ($class_row = $class_result->fetch_assoc()) {
        $classes[] = $class_row;
    }
}

// Handle form submission for creating fee voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $fee_amount = $_POST['fee_amount'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Insert fee voucher details into the fee_vouchers table
    $stmt = $conn->prepare("INSERT INTO fee_vouchers (class_id, fee_amount, month, year, issue_date, due_date) VALUES (?, ?, ?, ?, ?, ?)");
    $issue_date = date('Y-m-d'); // Current date as issue date
    $due_date = date('Y-m-d', strtotime('+30 days')); // 30 days from issue date as due date
    $stmt->bind_param("iissss", $class_id, $fee_amount, $month, $year, $issue_date, $due_date);

    if ($stmt->execute()) {
        $message = "Fee voucher created successfully!";
    } else {
        $message = "Error creating fee voucher: " . $stmt->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Fee Voucher</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <div class="container">
        <h3>Create Fee Voucher</h3>

        <!-- Display message -->
        <?php if (isset($message)): ?>
            <p class="message"><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group">
                <label for="class_id">Select Class:</label>
                <select id="class_id" name="class_id" required>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= htmlspecialchars($class['class_id']); ?>">
                                <?= htmlspecialchars($class['class_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No classes available</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="month">Select Month:</label>
                <select id="month" name="month" required>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
            </div>

            <div class="form-group">
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" value="<?= date('Y'); ?>" required>
            </div>

            <div class="form-group">
                <label for="fee_amount">Fee Amount:</label>
                <input type="number" id="fee_amount" name="fee_amount" required>
            </div>

            <div class="form-group">
                <button type="submit">Create</button>
            </div>
        </form>
    </div>
</body>
</html>
