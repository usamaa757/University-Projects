<?php
session_start();
include 'edit_lesson_process.php';
include 'update_lesson.php';
include '../include_files/db_connection.php';

require('header.php');
?>

<div class="container mt-5">
<a href="lesson_list.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary mb-3">
    Back
</a>
    <div class="card">
        
        <div class="card-header">
            <h3>Update Lesson</h3>
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?lesson_id=' . $lesson_id; ?>" method="POST" enctype="multipart/form-data">
                <div class="result-output mb-3">
                    <?php
                    if (isset($_GET['result'])) {
                        $resultMsg = $_GET['result'];
                    } elseif (isset($_GET['error'])) {
                        $errorMsg = $_GET['error'];
                    }

                    if (!empty($errorMsg)) {
                        echo "<div class='alert alert-danger' role='alert'>$errorMsg</div>";
                    } elseif (!empty($resultMsg)) {
                        echo "<div class='alert alert-success' role='alert'>$resultMsg</div>";
                    }
                    ?>
                </div>

                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">

                <div class="form-group">
                    <label for="current_file">Current Video:</label>
                    <div>
                        <?php if (!empty($video_url)) : ?>
                            <video width="560" height="315" controls class="mb-3">
                                <source src="<?php echo $video_url; ?>" type="<?php echo $file_type; ?>">
                                Your browser does not support the video tag.
                            </video>
                            <p><strong>Title:</strong> <?php echo $title; ?></p>
                            <p>File Name: <?php echo $file_name; ?></p>
                        <?php else : ?>
                            <p>No video found for this lesson.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="lesson_file">New Video:</label>
                    <input type="file" name="lesson_file" id="lesson_file" class="form-control col-lg-3">
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control col-lg-3" value="<?php echo $start_date; ?>" required>
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control col-lg-3" value="<?php echo $end_date; ?>" required>
                </div>

                <button type="submit" class="btn btn-success">Update</button>
            </form>
        </div>
    </div>
</div>
