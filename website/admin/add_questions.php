<?php
ob_start();
include 'header.php';
include "../db_connection.php";

$exam_id = $_GET['exam_id'];
$subject_id = $_GET['subject_id'] ?? null;

if (!$subject_id) {
    echo '<div class="alert alert-danger">No subject ID provided in the URL.</div>';
    exit;
}

// Fetch the exam type and subject name based on exam_id and subject_id
$exam_sql = "SELECT exam_type FROM mid_exams WHERE exam_id = ?";
$exam_stmt = $conn->prepare($exam_sql);
$exam_stmt->bind_param("i", $exam_id);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result();
$exam_type = $exam_result->num_rows > 0 ? $exam_result->fetch_assoc()['exam_type'] : null;
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
$subject_sql = "SELECT subject_name FROM subjects WHERE subject_id = ?";
$subject_stmt = $conn->prepare($subject_sql);
$subject_stmt->bind_param("i", $subject_id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();
$subject_name = $subject_result->num_rows > 0 ? $subject_result->fetch_assoc()['subject_name'] : null;

if (!$subject_name || !$exam_type) {
    echo '<div class="alert alert-danger">No subject or exam type found for the provided IDs.</div>';
    exit;
}

// Form submission logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_text = $_POST['question'];
    $options = [$_POST['option1'], $_POST['option2'], $_POST['option3'], $_POST['option4']];
    $correct_answer = $_POST['correct_answer'];

    // Check if question text already exists for the same exam_id
    $check_question_sql = "SELECT * FROM $questions_table WHERE exam_id = ? AND question_text = ?";
    $check_stmt = $conn->prepare($check_question_sql);
    $check_stmt->bind_param("is", $exam_id, $question_text);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Question already exists
        $_SESSION['error'] = "This question already exists in the exam!";
    } else {
        // Insert question
        $insert_question_sql = "INSERT INTO $questions_table (exam_id, subject_id, question_text) VALUES (?, ?, ?)";
        $question_stmt = $conn->prepare($insert_question_sql);
        $question_stmt->bind_param("iis", $exam_id, $subject_id, $question_text);
        $question_stmt->execute();
        $question_id = $question_stmt->insert_id;

        // Insert options and mark the correct one
        $insert_option_sql = "INSERT INTO $options_table (question_id, option_text,  is_correct) VALUES (?, ?, ?)";
        $option_stmt = $conn->prepare($insert_option_sql);

        foreach ($options as $index => $option_text) {
            $is_correct = ($index + 1 == $correct_answer) ? 1 : 0;
            $option_stmt->bind_param("isi", $question_id, $option_text, $is_correct);
            $option_stmt->execute();
        }

        $_SESSION['success'] = "Question and options added successfully!";
        header("Location: " . $_SERVER['PHP_SELF'] . "?exam_id=$exam_id&subject_id=$subject_id");
        exit;
    }
}
?>
<br><br><br><br><br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0 text-white"><?php echo htmlspecialchars($subject_name); ?> -
                        <?php echo ucfirst($exam_type); ?> Exam
                    </h3>
                </div>
                <div class="card-body"> <a
                        href="edit_questions.php?exam_id=<?php echo $exam_id . "&subject_id=" . $subject_id . "&exam_type=" . $exam_type; ?>"
                        class="btn btn-secondary mb-3">Edit Questions</a>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Display Total Questions -->
                    <?php
                    $total_questions_sql = "SELECT COUNT(*) as total FROM mid_exam_questions WHERE exam_id = ?";
                    $stmt = $conn->prepare($total_questions_sql);
                    $stmt->bind_param("i", $exam_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $totalQuestions = $result->fetch_assoc()['total'] ?? 0;

                    echo '<h4>Total Questions: ' . $totalQuestions . '</h4><br>';
                    ?>

                    <!-- Question Form -->
                    <form
                        action="<?php echo $_SERVER['PHP_SELF'] . '?exam_id=' . $exam_id . '&subject_id=' . $subject_id; ?>"
                        method="post">
                        <div class="form-group">
                            <label for="question">Question:</label>
                            <textarea id="question" name="question" rows="4" class="form-control" required></textarea>
                        </div>

                        <!-- Options Input with Radio Buttons -->
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="form-group">
                                <label for="option<?php echo $i; ?>">Option <?php echo $i; ?>:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text m-2 ">
                                            <input type="radio" name="correct_answer" class="form-check-input"
                                                value="<?php echo $i; ?>" required>
                                        </div>
                                    </div>
                                    <input type="text" id="option<?php echo $i; ?>" name="option<?php echo $i; ?>"
                                        class="form-control" required>
                                </div>
                            </div>
                        <?php endfor; ?>

                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$exam_stmt->close();
$subject_stmt->close();
$conn->close();
ob_end_flush();
?>