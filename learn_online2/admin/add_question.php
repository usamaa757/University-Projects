<?php

include '../include_files/db_connection.php';
include 'upload_quiz_process.php';

require('header.php');
?>


<!-- Add Qiestion Form -->
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Quiz Content -->
            <h3><?php echo $course_name; ?></h3>
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?quiz_id=' . $_GET['quiz_id']; ?>" method="post">
                <div class="quiz-content">
                    <div class="form-group">
                        <label for="question">Question:</label>
                        <textarea class="form-control" id="question" name="question" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="option1">Option 1:</label>
                        <input type="text" class="form-control" id="option1" name="option1" required>
                    </div>

                    <div class="form-group">
                        <label for="option2">Option 2:</label>
                        <input type="text" class="form-control" id="option2" name="option2" required>
                    </div>

                    <div class="form-group">
                        <label for="option3">Option 3:</label>
                        <input type="text" class="form-control" id="option3" name="option3" required>
                    </div>

                    <div class="form-group">
                        <label for="option4">Option 4:</label>
                        <input type="text" class="form-control" id="option4" name="option4" required>
                    </div>

                    <div class="form-group">
                        <label for="correct_answer">Correct Answer:</label>
                        <select class="form-control" id="correct_answer" name="correct_answer" required>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                            <option value="4">Option 4</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group mt-4 col-lg-4">
                        <button type="submit" class="btn btn-primary btn-block" name="submit">Upload</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

