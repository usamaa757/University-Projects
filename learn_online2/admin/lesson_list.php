<?php
// Include your database connection file
include '../include_files/db_connection.php';
session_start();

if(isset($_GET['course_id'])){
    $course_id = $_GET["course_id"];
    $courses = array();

    // Fetch student course for marking
    $fetch_course_query = "SELECT lessons.*, courses.course_name 
                           FROM lessons 
                           INNER JOIN courses ON lessons.course_id = courses.course_id
                           WHERE lessons.course_id = ?";
    $stmt = $conn->prepare($fetch_course_query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $course_result = $stmt->get_result();

    if ($course_result->num_rows > 0) {
        // Output the course for marking
        while ($row = $course_result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
}
 
$stmt->close();
require('header.php');
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $courses[0]['course_name'];?></h3>
                </div>
                <div class="card-body">
                    <div class="result-output mb-3">
                        <?php
                        if (!empty($errorMsg)) {
                            echo "<div class='alert alert-danger'>$errorMsg</div>";
                        } elseif (!empty($resultMsg)) {
                            echo "<div class='alert alert-success'>$resultMsg</div>";
                        }
                        ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>S No</th>
                                    <th>Course ID</th>
                                    <th>Lesson ID</th>
                                    <th>Lesson Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th colspan="2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($courses as $key => $course) {
                                    echo "<tr>";
                                    echo "<td>" . ($key + 1) . "</td>";
                                    echo "<td>" . $course["course_id"] . "</td>";
                                    echo "<td>" . $course["lesson_id"] . "</td>";
                                    echo "<td>" . $course["title"] . "</td>";
                                    echo "<td>" . $course["start_date"] . "</td>";
                                    echo "<td>" . $course["end_date"] . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-primary btn-sm' onclick=\"window.location.href='edit_lesson.php?lesson_id=" . $course['lesson_id'] . "&course_id=" .$course['course_id']."'\">Edit</button>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-danger btn-sm' onclick=\"if(confirm('Do you want to delete this lesson?')) { window.location.href='delete_lesson.php?lesson_id=" . $course['lesson_id'] . "&course_id=" .$course['course_id']."'; }\">Delete</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</div>
