<?php
include 'db.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Management - Display Data</title>
    <link rel="stylesheet" href="styles.css">
    <style>

    </style>
</head>

<body>

    <div class="list-container">
        <!-- Student List -->
        <div class="section">
            <h2>Student List</h2>
            <table border="1">
                <tr>
                    <th>Roll #</th>
                    <th>Name</th>
                    <th>Eamil</th>
                </tr>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM students");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . $row['student_id'] . "</td>
                            <td>" . $row['student_name'] . "</td>
                            <td>" . $row['email'] . "</td>
                          </tr>";
                }
                ?>
            </table>
        </div>

        <!-- Courses List -->
        <div class="section">
            <h2>Courses List</h2>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                </tr>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM courses");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . $row['course_id'] . "</td>
                            <td>" . $row['course_name'] . "</td>
                          </tr>";
                }
                ?>
            </table>
        </div>

        <!-- Exam Schedule -->
        <div class="section">
            <h2>Exam Schedule</h2>
            <table border="1">
                <tr>
                    <th>Exam ID</th>
                    <th>Course</th>
                    <th>Date</th>
                </tr>
                <?php
                $result = mysqli_query(
                    $conn,
                    "SELECT exam_schedule.*, courses.course_name
                     FROM exam_schedule 
                     INNER JOIN courses ON exam_schedule.course_id = courses.course_id"
                );
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . $row['exam_id'] . "</td>
                            <td>" . $row['course_name'] . "</td>
                            <td>" . $row['exam_date'] . "</td>
                          </tr>";
                }
                ?>
            </table>
        </div>
    </div>

</body>

</html>