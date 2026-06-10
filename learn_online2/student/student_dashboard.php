<?php
require('header.php');
include '../include_files/course_fetching_process.php';


if (!isset($_SESSION['student_email'])) {

	header("Location: student_login.php");
	exit();
}


?>

	<!-- Student Dashboard -->
<div class="col-lg-6 mt-5" >
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h3><?php echo $courses[0]['course_name']; ?></h3>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php foreach ($courses as $course) : ?>
                    <li class="list-group-item">
                        <a href="submit_assignment.php?course_id=<?php echo $course['course_id']; ?>">
                            <h5 class="card-title">Assignments</h5>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <ul class="list-group">
                <?php foreach ($courses as $course) : ?>
                    <li class="list-group-item">
                        <a href="quiz.php?course_id=<?php echo $course['course_id']; ?>">
                            <h5 class="card-title mt-3">Quizzes</h5>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
          
            <ul class="list-group">
                <?php foreach ($courses as $course) : ?>
                    <li class="list-group-item">
                        <a href="lesson_list.php?course_id=<?php echo $course['course_id']; ?>">
                            <h5 class="card-title mt-3">Lessons</h5>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

    <!-- Bootstrap JS and dependencies -->
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
