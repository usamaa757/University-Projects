<?php
// Include your database connection file
include '../include_files/db_connection.php';

// SQL query
$query = "SELECT sa.assignment_id, sa.student_id, r.student_name, sa.marks, c.course_name
          FROM student_assignment sa
          JOIN registration r ON sa.student_id = r.student_id
          JOIN assignments a ON sa.assignment_id = a.assignment_id
          JOIN courses c ON a.course_id = c.course_id
          ORDER BY sa.assignment_id, sa.student_id";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Initialize an array to store assignments
    $assignments = [];

    // Fetch each row as an associative array
    while ($row = $result->fetch_assoc()) {
        // Store assignment details in the array
        $assignments[] = [
            'assignment_id' => $row['assignment_id'],
            'student_id' => $row['student_id'],
            'student_name' => $row['student_name'],
            'course_name' => $row['course_name'],
            'marks' => $row['marks']
        ];
    }
}

// Close connection
$conn->close();

require('header.php');
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">S No</th>
                        <th scope="col">Assignment ID</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Student ID</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Action</th>
                        <th scope="col">Marks</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($assignments as $key => $assignment) {
                        echo "<tr>";
                        echo "<td>" . ($key + 1) . "</td>";
                        echo "<td>" . $assignment["assignment_id"] . "</td>";
                        echo "<td>" . $assignment["course_name"] . "</td>";
                        echo "<td>" . $assignment["student_id"] . "</td>";
                        echo "<td>" . $assignment["student_name"] . "</td>";

                        $marks = $assignment["marks"];
                        if (is_null($marks) || $marks === '' || $marks == 0) {
                            echo "<td><a href='assignment_marking.php?assignment_id=" . $assignment['assignment_id'] . "&student_id=" . $assignment['student_id'] . "' class='btn btn-primary'>Add Marks</a></td>";
                            echo "<td>Please add marks</td>";
                        } else {
                            echo "<td><a href='assignment_marking.php?assignment_id=" . $assignment['assignment_id'] . "&student_id=" . $assignment['student_id'] . "' class='btn btn-primary'>Add Marks</a></td>";
                            echo "<td>" . $assignment['marks'] . "</td>";
                        }
                        echo "<td><a href='edit_assignment.php?assignment_id=" . $assignment['assignment_id'] . "&student_id=" . $assignment['student_id'] . "' class='btn btn-success'>View</a></td>";

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
