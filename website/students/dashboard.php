<?php
include 'header.php';
include '../db_connection.php';
?>
<br><br><br><br><br>
<div class="container ">
    <!-- Dashboard Title -->
    <h2 class="text-center text-primary mb-4">Student Exam Dashboard</h2>

    <!-- Exam Selection Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="text-white">Select Exam and Number of Questions</h3>
        </div>
        <div class="card-body">
            <form method="POST" class="form-inline justify-content-center">
                <div class="form-group mr-3">
                    <label for="exam_type" class="mr-2">Exam Type:</label>
                    <select id="exam_type" name="exam_type" class="form-control" required>
                        <option value="Mid">Mid Exam</option>
                        <option value="Final">Final Exam</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label for="subject_id" class="mr-2">Subject:</label>
                    <select id="subject_id" name="subject_id" class="form-control" required>
                        <!-- Fetch and display subjects from database -->
                        <?php
                        $subjectResult = $conn->query("SELECT * FROM subjects");
                        while ($subject = $subjectResult->fetch_assoc()) {
                            echo "<option value='{$subject['subject_id']}'>{$subject['subject_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label for="total_questions" class="mr-2">Total Questions:</label>
                    <input type="number" id="total_questions" name="total_questions" class="form-control" min="1"
                        required>
                </div>

                <button type="submit" class="btn btn-success">Generate Questions</button>
            </form>
        </div>
    </div>
    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'):
        // Get selected parameters from the form
        $exam_type = $_POST['exam_type'];
        $subject_id = $_POST['subject_id'];
        $totalQuestions = (int) $_POST['total_questions'];

        // Determine the correct tables based on exam type
        if ($exam_type === 'Mid') {
            $table_name = 'mid_exams';
            $questions_table = 'mid_exam_questions';
            $options_table = 'mid_exam_options';
        } elseif ($exam_type === 'Final') {
            $table_name = 'final_exams';
            $questions_table = 'final_exam_questions';
            $options_table = 'final_exam_options';
        } else {
            die("Invalid exam type specified.");
        }

        // Fetch exam_id based on subject_id from the exam table
        $examQuery = $conn->prepare("SELECT exam_id FROM $table_name WHERE subject_id = ?");
        $examQuery->bind_param("i", $subject_id);
        $examQuery->execute();
        $examResult = $examQuery->get_result();

        // Check if an exam was found
        if ($examRow = $examResult->fetch_assoc()) {
            $exam_id = $examRow['exam_id'];

            // Count total available questions for the selected exam_id
            $countQuery = $conn->prepare("SELECT COUNT(*) as total FROM $questions_table WHERE exam_id = ?");
            $countQuery->bind_param("i", $exam_id);
            $countQuery->execute();
            $countResult = $countQuery->get_result();
            $countRow = $countResult->fetch_assoc();
            $totalAvailableQuestions = $countRow['total'];

            // Check if selected questions exceed available questions
            if ($totalQuestions > $totalAvailableQuestions) {
                echo "<div class='alert alert-danger text-center mt-4'>You have selected <strong>$totalQuestions</strong> questions, but only <strong>$totalAvailableQuestions</strong> are available.</div>";
            } else {
                // Randomly select questions from the appropriate table
                $query = $conn->prepare("SELECT * FROM $questions_table WHERE exam_id = ? ORDER BY RAND() LIMIT ?");
                $query->bind_param("ii", $exam_id, $totalQuestions);
                $query->execute();
                $result = $query->get_result();

                $questions = [];
                while ($row = $result->fetch_assoc()) {
                    $questions[] = $row;
                }

                // Fetch the subject name based on the selected subject ID
                $subjectQuery = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
                $subjectQuery->bind_param("i", $subject_id);
                $subjectQuery->execute();
                $subjectResult = $subjectQuery->get_result();
                $subjectName = '';
                if ($subjectRow = $subjectResult->fetch_assoc()) {
                    $subjectName = $subjectRow['subject_name'];
                }
                ?>

                <!-- Selected Exam Section -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="text-center"><?php echo htmlspecialchars($exam_type); ?>-Term Exam
                            <?php echo htmlspecialchars($subjectName); ?>
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="alert alert-info">
                            <h5 class="">You have selected
                                <strong><?php echo htmlspecialchars($totalQuestions); ?></strong>
                                <span>out of
                                </span>
                                <strong><?php echo htmlspecialchars($totalAvailableQuestions); ?></strong>
                            </h5>
                        </div>
                        <a href="take_exam.php?exam_type=<?php echo urlencode($exam_type); ?>&subject_id=<?php echo urlencode($subject_id); ?>&total_questions=<?php echo urlencode($totalQuestions); ?>"
                            class="btn btn-primary btn-lg">Take Exam</a>
                    </div>
                </div>

            <?php } // End of if block for valid exam_id ?>
        <?php } else {
            echo "<div class='alert alert-danger text-center mt-4'>Error: No exam found for the selected subject.</div>";
        }
    endif;
    include '../footer.php'; ?>

    </body>

    </html>