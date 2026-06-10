<?php
// Include your database connection file
include '../include_files/db_connection.php';

$quizzes = array();

// Fetch student quizzes for marking along with student name
$fetch_quizzes_query = "
    SELECT q.quiz_id, q.course_name, sq.marks, sq.student_id, sr.student_name
    FROM quizzes q
    JOIN student_quiz sq ON q.quiz_id = sq.quiz_id
    JOIN registration sr ON sq.student_id = sr.student_id
";

$result = mysqli_query($conn, $fetch_quizzes_query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $quizzes[] = $row;
    }
} else {
    echo "Quiz is not submitted";
}
require('header.php');
?>
<div class="container mt-5">
    <a href="quiz_list.php" class="btn btn-primary mb-3">Back</a>

    <div class="card">
        <div class="card-header">
            <h3>Submitted Quiz's List</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>S No</th>
                            <th>Quiz ID</th>
                            <th>Course Name</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($quizzes as $key => $quiz) {
                            echo "<tr>";
                            echo "<td>" . ($key + 1) . "</td>";
                            echo "<td>" . $quiz["quiz_id"] . "</td>";
                            echo "<td>" . $quiz["course_name"] . "</td>";
                            echo "<td>" . $quiz["student_id"] . "</td>";
                            echo "<td>" . $quiz["student_name"] . "</td>";
                            echo "<td>" . $quiz['marks'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
