<?php
ob_start();
include 'header.php';
include '../db_connection.php';

$exam_type = $_GET['exam_type'];
$subject_id = $_GET['subject_id'];
$total_questions = $_GET['total_questions'];
$student_id = $_SESSION['student_id'];

if ($exam_type === "Mid") {
    $table_name = "mid_exams";
    $questions_table = "mid_exam_questions";
    $options_table = "mid_exam_options";
} elseif ($exam_type === "Final") {
    $table_name = "final_exams";
    $questions_table = "final_exam_questions";
    $options_table = "final_exam_options";
} else {
    die("Invalid exam type.");
}

if (!isset($_SESSION['question_token'])) {
    $_SESSION['question_token'] = bin2hex(random_bytes(32));
}

$exam_sql = "SELECT exam_id FROM $table_name WHERE exam_type = ? AND subject_id = ?";
$exam_stmt = $conn->prepare($exam_sql);
$exam_stmt->bind_param("si", $exam_type, $subject_id);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result();

if ($exam_result->num_rows > 0) {
    $exam_data = $exam_result->fetch_assoc();
    $exam_id = $exam_data['exam_id'];
    $_SESSION['exam_id'] = $exam_id;

    if (!isset($_SESSION['student_exam_id'])) {
        $insert_exam_sql = "INSERT INTO student_exam (student_id, exam_id, subject_id, exam_type) VALUES (?, ?, ?, ?)";
        $insert_exam_stmt = $conn->prepare($insert_exam_sql);
        $insert_exam_stmt->bind_param("iiis", $student_id, $exam_id, $subject_id, $exam_type);
        $insert_exam_stmt->execute();
        $_SESSION['student_exam_id'] = $conn->insert_id;
    }

    if (!isset($_SESSION['question_index'])) {
        $_SESSION['question_index'] = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $question_id = $_POST['question_id'];
        $selected_option = $_POST['answer'];
        $student_exam_id = $_SESSION['student_exam_id'];

        if (!isset($_SESSION['total_marks'])) {
            $_SESSION['total_marks'] = 0;
        }

        $correct_option_sql = "SELECT option_id FROM $options_table WHERE question_id = ? AND is_correct = 1";
        $correct_option_stmt = $conn->prepare($correct_option_sql);
        $correct_option_stmt->bind_param("i", $question_id);
        $correct_option_stmt->execute();
        $correct_option_result = $correct_option_stmt->get_result();

        if ($correct_option_result->num_rows > 0) {
            $correct_option = $correct_option_result->fetch_assoc()['option_id'];
            if ($selected_option == $correct_option) {
                $_SESSION['total_marks'] += 1;
            }
        }

        $insert_answer_sql = "INSERT INTO student_exam_answers (student_exam_id, question_id, selected_option_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE selected_option_id = ?";
        $insert_answer_stmt = $conn->prepare($insert_answer_sql);
        $insert_answer_stmt->bind_param("iiii", $student_exam_id, $question_id, $selected_option, $selected_option);
        $insert_answer_stmt->execute();

        if (isset($_POST['submit_exam'])) {
            $update_marks_sql = "UPDATE student_exam SET marks = ? WHERE student_exam_id = ?";
            $update_marks_stmt = $conn->prepare($update_marks_sql);
            $update_marks_stmt->bind_param("ii", $_SESSION['total_marks'], $student_exam_id);
            $update_marks_stmt->execute();

            unset($_SESSION['exam_id']);
            unset($_SESSION['student_exam_id']);
            unset($_SESSION['question_index']);
            unset($_SESSION['total_marks']);

            header("Location: exam_result.php?student_id=" . $_SESSION['student_id'] . "&student_exam_id=" . $student_exam_id . "&exam_type=" . $exam_type);
            exit();
        } else {
            $_SESSION['question_index']++;
            unset($_SESSION['current_question_id']);
            header("Location: take_exam.php?exam_type=$exam_type&subject_id=$subject_id&total_questions=$total_questions");
            exit();
        }
    }

    $current_question_index = $_SESSION['question_index'];

    if (isset($_SESSION['current_question_id']) && $_SESSION['question_index'] == $current_question_index) {
        $question_id = $_SESSION['current_question_id'];
    } else {
        // Fetch a random question related to the specific subject
        $sql = "SELECT question_id, question_text FROM $questions_table WHERE exam_id = ? AND subject_id = ? ORDER BY RAND() LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $exam_id, $subject_id); // Bind exam_id and subject_id
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $question = $result->fetch_assoc();
            $question_id = $question['question_id'];
            $_SESSION['current_question_id'] = $question_id;
            $_SESSION['current_question_text'] = $question['question_text'];
        } else {
            echo "No questions found for this subject.";
            exit();
        }
    }
} else {
    echo "Exam not found for the specified type and subject.";
    exit();
}
ob_end_flush();
?>
<br><br><br><br><br>
<!-- HTML Code for Displaying the Question and Options -->
<div class="container">
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="text-white">
                <?php echo htmlspecialchars($exam_type); ?> Exam - Question
                <?php echo $_SESSION['question_index'] + 1; ?> of <?php echo $total_questions; ?>
            </h3>
        </div>
        <div class="card-body">
            <form
                action="take_exam.php?exam_type=<?php echo $exam_type; ?>&subject_id=<?php echo $subject_id; ?>&total_questions=<?php echo $total_questions; ?>"
                method="post">
                <div class="lead p-3">
                    <?php echo htmlspecialchars($_SESSION['current_question_text']); ?>
                </div>
                <ul class="list-group">
                    <?php
                    // Fetch options for the current question
                    $options_sql = "SELECT option_id, option_text FROM $options_table WHERE question_id = ?";
                    $options_stmt = $conn->prepare($options_sql);
                    $options_stmt->bind_param("i", $_SESSION['current_question_id']);
                    $options_stmt->execute();
                    $options_result = $options_stmt->get_result();

                    while ($option = $options_result->fetch_assoc()) {
                        echo '<li class="list-group-item">
                                <label class="form-check-label p-3 ps-3 pe-2">
                                    <input type="radio" name="answer" value="' . $option['option_id'] . '" class="form-check-input" required>
                                    ' . htmlspecialchars($option['option_text']) . '
                                </label>
                              </li>';
                    }
                    ?>
                </ul>
                <div class="d-flex justify-content-end mt-3">
                    <input type="hidden" name="question_id" value="<?php echo $_SESSION['current_question_id']; ?>">
                    <?php if ($_SESSION['question_index'] < $total_questions - 1): ?>
                        <button type="submit" name="next" class="btn btn-primary">Next</button>
                    <?php else: ?>
                        <button type="submit" name="submit_exam" class="btn btn-success">Submit Exam</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Close connection only at the very end
$conn->close();
?>