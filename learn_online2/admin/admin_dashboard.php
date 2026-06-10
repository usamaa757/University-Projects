<?php
include '../include_files/fetch_table_data.php';
$courses = fetchTableData('courses');

require('header.php');
?>

<!-- Admin Dashboard -->
<div class="col-lg-5 mt-4">
    <?php $sno = 1;
    foreach ($courses as $course) : ?>
        <div class="card mb-3">
            <div class="card-header bg-dark text-light">
                <h3><?php echo $sno++ . ". " . $course['course_name']; ?></h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <a href="upload_assignment.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary">Upload Assignments</a>
                    <a href="upload_quiz.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary">Upload Quizzes</a>
                    <a href="upload_lesson.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary">Upload Lessons</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<!-- Bootstrap JS and dependencies -->
<script src="js/jquery-3.5.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>

</html>