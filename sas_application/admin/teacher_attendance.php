<?php
include 'header.php';
ob_start();
// Include the database connection file
include '../other/db_connection.php';

// Fetch teachers with status 'approved'
$stmt = $conn->prepare("SELECT teacher_id, teacher_name FROM teachers WHERE status = 'approved'");
$stmt->execute();
$result = $stmt->get_result();

// Close the connection
$conn->close();
ob_end_clean();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="../css/form.css">
    <style>
       
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
       
        .form-group {
            text-align: center;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h2>Mark Attendance</h2>
        <?php
            // Display message if available
            if (isset($_GET['message'])) {
                echo "<p class='message'>" . htmlspecialchars($_GET['message']) . "</p>";
            }
            ?>
        <form action="process_attendance.php" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Teacher Name</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                        <td>
                            <input type="radio" name="attendance[<?php echo $row['teacher_id']; ?>]" value="present" required> Present
                            <input type="radio" name="attendance[<?php echo $row['teacher_id']; ?>]" value="absent"> Absent
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
