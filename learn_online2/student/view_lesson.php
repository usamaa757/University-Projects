<?php

include '../include_files/db_connection.php';

$lecture_title = '';
$video_url = '';

if (isset($_GET['course_id'])) {
  $course_id = intval($_GET['course_id']); // Validate course_id (example)
  $sql = "SELECT course_name FROM courses WHERE course_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $course_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($course = $result->fetch_assoc()) {
    $course_name = $course['course_name'];
  }
  $lesson_id = (isset($_GET['lesson_id'])) ? intval($_GET['lesson_id']) : ''; // Validate lesson_id (optional)

  // ... (database connection code)

  $sql = "SELECT * FROM lessons WHERE course_id = $course_id"; // Assuming lesson_id is optional
  if ($lesson_id) {
    $sql .= " AND lesson_id = $lesson_id";
  }

  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    $lecture = mysqli_fetch_assoc($result);
    $file_name = $lecture['file_name'];
    $video_url = "../admin/uploads/videos/" . $file_name;
  }
}
require('header.php');
?>
<div class="container mt-5">
    <div class="d-flex justify-content-start mb-3">
        <a href="lesson_list.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary">
            Back
        </a>
    </div>

    <div id="quiz-container" class="card">
        <div class="card-header">
            <h3 class="m-0"><?php echo $course_name ?></h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="<?php echo $video_url; ?>" class="btn btn-success">
                    Download Lesson
                </a>
            </div>

            <div class="text-center">
                <?php if (!empty($video_url)) : ?>
                    <video width="560" height="315" controls class="img-fluid">
                        <source src="<?php echo $video_url; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
