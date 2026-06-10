<?php
include 'student_assingment_fetch_process.php';
include 'assignment_submission_process.php';
require('header.php');

?>
<div class="container mt-4">
    <div class="page-header">
        <?php if (!empty($assignments)) : ?>
            <h3 class="display-4"><?php echo $assignments[0]['course_name']; ?></h3>
        <?php else : ?>
            <h3 class="display-4">No Assignments Found</h3>
        <?php endif; ?>

        <?php 
        $sno = 1;
        // Loop through the assignments array to populate HTML divs
        foreach ($assignments as $assignment) {
        ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Assignment No: <?php echo $sno++; ?></h3>
                </div>
                <div class="card-body">
                    <p><strong>Assignment Question:</strong> <?php echo htmlspecialchars($assignment["assignment_question"]); ?></p>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?assignment_id=' . $assignment['assignment_id'] . '&course_id=' . $assignment['course_id']); ?>">
                        <div class="form-group">
                            <label for="assignment_answer">Answer:</label>
                            <textarea class="form-control" id="assignment_answer" rows="6" name="assignment_answer" placeholder="Enter your answer here"></textarea>
                        </div>
           
                        <input type="hidden" name="course_id" value="<?php echo $assignment['course_id']; ?>">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                        <button type="submit" class="btn btn-primary">Submit Answer</button>
                    </form>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>