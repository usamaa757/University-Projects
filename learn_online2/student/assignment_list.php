<?php
session_start();

if (!isset($_SESSION['student_email'])) {
    header("Location: ../../login/student_login.php");
    exit();
}

require('header.php');
?>
<?php
include '../include_files/db_connection.php';

$submissions = array();

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch submitted assignments with course names
    $sql = "SELECT c.course_name, a.start_date, a.end_date, sa.marks, sa.status
            FROM courses c
            INNER JOIN assignments a ON c.course_id = a.course_id
            INNER JOIN student_assignment sa ON a.assignment_id = sa.assignment_id
            WHERE sa.student_id = ? AND sa.status = 'Submitted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Store results in an associative array for quick lookup
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }
}
?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h3 class="m-0">Submitted Status</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>S No</th>
                        <th>Course Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($submissions)) {
                        $sn = 1;
                        foreach ($submissions as $submission) {
                            echo "<tr>";
                            echo "<td>" . $sn++ . "</td>";
                            echo "<td>" . $submission['course_name'] . "</td>";
                            echo "<td>" . $submission['start_date'] . "</td>";
                            echo "<td>" . $submission['end_date'] . "</td>";
                            echo "<td>";
                            if ($submission['status'] == 'Submitted') {
                                echo $submission['marks'];
                            } else {
                                echo "<span class='badge badge-danger'>Not Submitted</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No submitted assignments found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
