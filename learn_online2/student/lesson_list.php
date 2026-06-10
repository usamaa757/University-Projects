<?php

include '../include_files/db_connection.php';
$lessons = array();
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    $sql = "SELECT course_name FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($course = $result->fetch_assoc()) {
        $course_name = $course['course_name'];
    }
    // Retrieve lesson details for the student from the database
    $sql = "SELECT * FROM lessons WHERE course_id = '$course_id'";
    $result = mysqli_query($conn, $sql);

    // Check if lessons are found for the student
    if (mysqli_num_rows($result) > 0) {
        while ($lesson = mysqli_fetch_assoc($result)) {
            $lessons[] = $lesson;




            // Construct the URL to the video file
            $video_url = "../admin/uploads/videos/" . $lesson['file_name']; // Adjust the directory name if necessary

        }
    }
}
require('header.php');
?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <?php if (!empty($course_name)) : ?>
                <h3 class="m-0"> <?php echo $course_name; ?></h3>
            <?php endif ?>
        </div>
        <div class="card-body">
            <?php
            if (!empty($lessons)) {
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered'>";
                echo "<thead class='thead-light'>";
                echo "<tr>
                        <th>S No</th>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                      </tr>";
                echo "</thead>";
                echo "<tbody>";
                $sno = 1;

                foreach ($lessons as $lesson) {
                    $lesson_title = $lesson['title']; // Assuming 'title' is the correct key
                    $start_date = $lesson['start_date'];
                    $end_date = $lesson['end_date'];
                    $lesson_id = $lesson['lesson_id']; // Fetch lesson_id from the $lesson array
                    echo "<tr>";
                    echo "<td>" . $sno++ . "</td>";
                    echo "<td><a href='view_lesson.php?course_id=$course_id&lesson_id=$lesson_id'>$lesson_title</a></td>";
                    echo "<td>$start_date</td>";
                    echo "<td>$end_date</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-warning' role='alert'>No lessons found for the specified subject.</div>";
            }
            ?>
        </div>
    </div>
</div>
