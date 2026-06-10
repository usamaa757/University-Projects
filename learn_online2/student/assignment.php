<?php
include 'student_assingment_fetch_process.php';

require('header.php');

?>
<!-- Assignment -->
<a href="view_assignment.php?subject_id=<?php echo $subject_id; ?>" class="btn btn-secondary mb-3">Back</a>
<div class="container mt-4">
    <div class="page-header">
        <?php if (!empty($assignments)) : ?>
            <h3 class="display-4"><?php echo $assignments[0]['course_name']; ?></h3>
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
                    <p><strong>Assignment Question:</strong> <?php echo $assignment["assignment_question"]; ?></p>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?assignment_id=' . $assignment['assignment_id'] . '&subject_id=' . $assignment['subject_id']; ?>">
                        <div class="form-group">
                            <label for="assignment_answer">Answer:</label>
                            <textarea class="form-control" id="assignment_answer" rows="6" name="assignment_answer" placeholder="Enter your answer here"></textarea>
                        </div>
           
                        <input type="hidden" name="subject_id" value="<?php echo $assignment['course_id']; ?>">

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
