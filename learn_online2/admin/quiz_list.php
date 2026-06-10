<?php
require('header.php');
?>
<div class="container mt-5">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">S No</th>
                <th scope="col">Quiz ID</th>
                <th scope="col">Course Name</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Status</th>
                <th scope="col" colspan="3" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include "../include_files/fetch_table_data.php";
            include "../include_files/db_connection.php";

            // Fetching quizzes data from database
            $fetch_quiz = fetchTableData('quizzes');
            if ($fetch_quiz) {
                $sNo = 1;
                foreach ($fetch_quiz as $row) {
                    // Check if quiz is taken by any student
                    $quiz_taken = false;
                    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM student_quiz WHERE quiz_id = ?");
                    $check_stmt->bind_param("i", $row['quiz_id']);
                    $check_stmt->execute();
                    $check_stmt->bind_result($count);
                    $check_stmt->fetch();
                    if ($count > 0) {
                        $quiz_taken = true;
                    }
                    $check_stmt->close();

                    // Check if quiz has expired
                    $current_date = date('Y-m-d');
                    $end_date = $row['end_date'];
                    $is_expired = $current_date > $end_date;

                    // Determine status based on expiration
                    $status_label = $is_expired ? '<span style="color:red;">Closed</span>' : '<span style="color:green;">Open</span>';

                    echo "<tr>";
                    echo "<td>" . $sNo++ . "</td>";
                    echo "<td>" . $row['quiz_id'] . "</td>";
                    echo "<td>" . $row['course_name'] . "</td>";
                    echo "<td>" . $row['start_date'] . "</td>";
                    echo "<td>" . $row['end_date'] . "</td>";
                    echo "<td>" . $status_label . "</td>";

                    echo "<td>";
                    echo "<button class='btn btn-sm btn-success' onclick=\"window.location.href='view_quiz.php?quiz_id=" . $row['quiz_id'] . "'\">View Details</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>
