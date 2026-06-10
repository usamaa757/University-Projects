<?php
include '../other/db_connection.php';
include 'header.php';
// Fetch teacher list from the database
$teacher_query = "SELECT teacher_id, teacher_name FROM teachers";
$teacher_result = $conn->query($teacher_query);

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $salary = $_POST['salary'];
    $deductions = $_POST['deductions'];

    // Calculate net salary
    $net_salary = $salary - $deductions;

    // Prepare SQL and bind parameters
    $stmt = $conn->prepare("INSERT INTO salary_slips (teacher_id, month, year, salary, deductions, net_salary) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddd", $teacher_id, $month, $year, $salary, $deductions, $net_salary);

    if ($stmt->execute()) {
        echo "Salary slip generated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Salary Slip</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <header>
        <h1>Generate Salary Slip</h1>
    </header>
    <form method="post">
        <div class="form-group">
            <label for="teacher_id">Teacher:</label>
            <select id="teacher_id" name="teacher_id" required>
                <option value="">Select a Teacher</option>
                <?php
                if ($teacher_result->num_rows > 0) {
                    while ($row = $teacher_result->fetch_assoc()) {
                        echo "<option value='" . $row['teacher_id'] . "'>" . $row['teacher_name'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="month">Month:</label>
            <input type="month" id="month" name="month" required>
        </div>

        <div class="form-group">
            <label for="year">Year:</label>
            <input type="number" id="year" name="year" min="1900" max="2100" required>
        </div>

        <div class="form-group">
            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="deductions">Deductions:</label>
            <input type="number" id="deductions" name="deductions" step="0.01" required>
        </div>

              <input type="submit" value="Generate Salary Slip">
    </form>
</body>
</html>
