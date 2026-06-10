<?php
session_start();
include '../other/db_connection.php';
$teacher_id = $_SESSION['user_id']; // Get the teacher's user ID from the session

// Retrieve the salary slips for the logged-in teacher
$sql = "SELECT * FROM salary_slips WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Salary Slip</title>
</head>
<body>
    <h1>My Salary Slips</h1>
    <?php
    // Display the salary slips
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Month</th><th>Year</th><th>Salary</th><th>Tax</th><th>Benefits</th><th>Net Salary</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['month'] . "</td>";
            echo "<td>" . $row['year'] . "</td>";
            echo "<td>" . $row['salary'] . "</td>";
            echo "<td>" . $row['tax'] . "</td>";
            echo "<td>" . $row['benefits'] . "</td>";
            echo "<td>" . $row['net_salary'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No salary slips available.";
    }
    ?>
</body>
</html>