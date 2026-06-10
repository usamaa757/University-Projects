
<?php
include 'header.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include '../other/db_connection.php'; // Database connection

        $student_id = $_POST['student_id'];
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];

        // Fetch student's name based on student_id
        $student_name_query = "SELECT student_name, class_id FROM students WHERE student_id = ?";
$stmt = $conn->prepare($student_name_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();

// Bind both variables in a single bind_result call
$stmt->bind_result($student_name, $class_id);

$stmt->fetch();
$stmt->close();


        if (empty($student_name)) {
            echo "<p style='color:red; text-align:center;'>Student not found!</p>";
            exit;
        }

        // Fetch attendance records within the date range for the student
        $attendance_query = "SELECT status, COUNT(*) AS count
                  FROM attendance
                  WHERE student_id = ? AND attendance_date BETWEEN ? AND ?
                  GROUP BY status";
        
        $stmt = $conn->prepare($attendance_query);
        if (!$stmt) {
            die("Query preparation failed: " . $conn->error);
        }
        
        $stmt->bind_param("iss", $student_id, $from_date, $to_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            // Initialize counts
            $total_present = 0;
            $total_absent = 0;

            // Calculate totals
            while ($row = $result->fetch_assoc()) {
                if (strtolower($row['status']) === 'present') {
                    $total_present = $row['count'];
                } elseif (strtolower($row['status']) === 'absent') {
                    $total_absent = $row['count'];
                }
            }

            // Calculate total days and attendance percentage
            $total_days = $total_present + $total_absent;
            $attendance_percentage = $total_days > 0 ? ($total_present / $total_days) * 100 : 0;

            $stmt->close();
            $conn->close();
        } else {
            echo "Error executing query: " . $conn->error;
        }
    
    }
    ?> 

        <!-- Display the attendance report below the form -->
       


    <style>
      
        h2 {
            text-align: center;
            color: #333;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 20px auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .report {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }
    </style>
    <br><br>
    <h2>Attendance Report</h2>
    <form action="" method="POST">
        <label for="student_id">Student ID:</label>
        <input type="text" id="student_id" name="student_id" required><br><br>

        <label for="from_date">From Date:</label>
        <input type="date" id="from_date" name="from_date" required><br><br>

        <label for="to_date">To Date:</label>
        <input type="date" id="to_date" name="to_date" required><br><br>

        <input type="submit" value="Generate Report">
    </form>
    <div class="report">
            <h3>Attendance Report </h3>
            <label for="name">Student Name:</label>
            <?= htmlspecialchars($student_name) ?>
            <label for="name">class:</label>
            <?= htmlspecialchars($class_id) ?>
            <p>Date Range: <?= htmlspecialchars($from_date) ?> to <?= htmlspecialchars($to_date) ?></p>
            <table>
                <tr>
                    <th>Total Present Days</th>
                    <td><?= isset($total_present) ? htmlspecialchars($total_present) : '0' ?></td>
                </tr>
                <tr>
                    <th>Total Absent Days</th>
                    <td><?= isset($total_absent) ? htmlspecialchars($total_absent) : '0' ?></td>
                </tr>
                <tr>
                   <th>Total Days</th>
                    <td><?= isset($total_days) ? htmlspecialchars($total_days) : '0' ?></td>
                </tr>
                <tr>
                    <th>Attendance Percentage</th>
                    <td><?= isset($attendance_percentage) ? number_format($attendance_percentage, 2) . '%' : '0%' ?></td>
                </tr>
            </table>
        </div>
</body>

</html>
