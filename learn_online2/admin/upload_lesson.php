

<?php
require('header.php');
include 'upload_lesson_process.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $course_name ?></h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?course_id=' . $course_id; ?>" method="post" enctype="multipart/form-data" class="lecture-form">
                        <div class="result-output mb-3">
                            <?php
                            if (!empty($errorMsg)) {
                                echo "<div class='alert alert-danger'>$errorMsg</div>";
                            } elseif (!empty($resultMsg)) {
                                echo "<div class='alert alert-success'>$resultMsg</div>";
                            }
                            ?>
                        </div>

                        <input type="hidden" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>">
                        <input type="hidden" id="course_id" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">

                        <div class="form-group">
                            <label for="start_date">From</label>
                            <input type="date" class="form-control col-md-4" id="start_date" name="start_date" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">To</label>
                            <input type="date" class="form-control col-md-4" id="end_date" name="end_date" required>
                        </div>

                        <div class="form-group">
                            <label for="title">Lesson Title</label>
                            <input type="text" class="form-control col-md-6" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="lesson_file">Upload Video</label>
                            <input type="file" class="form-control-file" id="lesson_file" name="lesson_file" accept=".mp4, .docx, .doc, .pdf" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Upload Lesson</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
