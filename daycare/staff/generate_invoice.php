<?php
// generate_invoice.php
session_start();
require_once '../db_connection.php'; // Include DB connection file


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the month and year from the form
    $child_id = $_POST['child_id'];
    $month = $_POST['month']; // For example, '2025-04'

    // Define daily rate (for example, $20 per day)
    $daily_rate = 20;

    // Fetch attendance data for the given child for the given month
    $sql = "SELECT COUNT(*) AS total_days
            FROM attendance
            WHERE child_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $child_id, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Calculate the total amount
    $total_days = $row['total_days'];
    $total_amount = $total_days * $daily_rate;

    // Generate the invoice and save it in the database
    $invoice_date = date("Y-m-d");
    $sql = "INSERT INTO invoices (child_id, total_amount, invoice_date)
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $child_id, $total_amount, $invoice_date);

    if ($stmt->execute()) {
        echo "Invoice generated successfully!";
    } else {
        echo "Error generating invoice: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Monthly Invoice</title>
</head>

<body>

    <h2>Generate Monthly Invoice</h2>

    <form action="generate_invoice.php" method="POST">
        <label for="child_id">Select Child:</label>
        <select name="child_id" required>
            <?php
            // Fetch all children from the database for the dropdown
            $sql = "SELECT child_id, name FROM children";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['child_id'] . "'>" . $row['name'] . "</option>";
            }
            ?>
        </select>
        <br><br>

        <label for="month">Select Month:</label>
        <input type="month" name="month" required>
        <br><br>

        <button type="submit">Generate Invoice</button>
    </form>

</body>

</html>