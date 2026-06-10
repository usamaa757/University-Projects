<?php
session_start();
include 'fetch_assignment_process.php';
include 'update_assignment.php';
require('header.php');

    $assignment_id = $_GET['assignment_id'];
  
?>
<a href="assignment_list.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-primary ml-4 mt-4">Back</a>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3>Update Assignment</h3>
        </div>
        <div class="card-body">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?assignment_id=<?php echo $assignment_id; ?>" method="POST">
                <div class="result-output mb-3">
                    <?php
                    if (!empty($errorMsg)) {
                        echo "<div class='alert alert-danger'>$errorMsg</div>";
                    } elseif (!empty($resultMsg)) {
                        echo "<div class='alert alert-success'>$resultMsg</div>";
                    }
                    ?>
                </div>
                <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <div class="form-group">
                    <label for="assignment_question"><h4>Assignment Question:</h4></label>
                    <textarea name="assignment_question" id="assignment_question" class="form-control" rows="5" required><?php echo $assignment_question; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date; ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $end_date; ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
