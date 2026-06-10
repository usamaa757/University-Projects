<?php
// Include required files
include 'header.php';
include '../db_connection.php';

// Initialize variables for rendering
$exam_details = [];
$questions_array = [];
$subject_name = "Unknown Subject";
$marks = 0;
$total_questions = 0;
$correct_answers = 0;
$wrong_answers = 0;

// Retrieve parameters from the URL
$student_exam_id = isset($_GET['student_exam_id']) ? intval($_GET['student_exam_id']) : 0;

if ($student_exam_id > 0) {
    // Fetch exam details
    $sql_exam_details = "SELECT se.exam_id, se.exam_type, se.subject_id FROM student_exam se WHERE se.student_exam_id = ?";
    $stmt_exam_details = $conn->prepare($sql_exam_details);
    $stmt_exam_details->bind_param("i", $student_exam_id);
    $stmt_exam_details->execute();
    $exam_details_result = $stmt_exam_details->get_result();

    if ($exam_details_result->num_rows > 0) {
        $exam_details = $exam_details_result->fetch_assoc();
        $exam_type = $exam_details['exam_type'];
        $subject_id = $exam_details['subject_id'];

        // Fetch subject name
        $sql_subject_name = "SELECT subject_name FROM subjects WHERE subject_id = ?";
        $stmt_subject_name = $conn->prepare($sql_subject_name);
        $stmt_subject_name->bind_param("i", $subject_id);
        $stmt_subject_name->execute();
        $subject_name_result = $stmt_subject_name->get_result();
        $subject_name = $subject_name_result->num_rows > 0 ? $subject_name_result->fetch_assoc()['subject_name'] : $subject_name;

        // Determine tables based on exam type
        $questions_table = $exam_type === 'Mid' ? 'mid_exam_questions' : 'final_exam_questions';
        $options_table = $exam_type === 'Mid' ? 'mid_exam_options' : 'final_exam_options';

        // Fetch student answers and related questions and options
        $sql = "
            SELECT sea.student_answer_id, sea.selected_option_id, q.question_id, q.question_text, o.option_id, o.option_text, o.is_correct
            FROM student_exam_answers sea
            LEFT JOIN $questions_table q ON sea.question_id = q.question_id
            LEFT JOIN $options_table o ON q.question_id = o.question_id
            WHERE sea.student_exam_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_exam_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Process questions and options
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];
            if (!isset($questions_array[$question_id])) {
                $questions_array[$question_id] = [
                    'question_text' => htmlspecialchars($row['question_text']),
                    'selected_option_id' => $row['selected_option_id'],
                    'options' => [],
                    'is_correct' => false,
                ];
            }

            $option = [
                'option_id' => $row['option_id'],
                'option_text' => htmlspecialchars($row['option_text']),
                'is_correct' => $row['is_correct']
            ];
            $questions_array[$question_id]['options'][] = $option;

            if ($option['option_id'] == $row['selected_option_id']) {
                $questions_array[$question_id]['is_correct'] = $row['is_correct'];
            }
        }

        // Calculate scores
        foreach ($questions_array as $question) {
            if ($question['is_correct']) {
                $correct_answers++;
            } else {
                $wrong_answers++;
            }
        }
        $total_questions = count($questions_array);
        $marks = $correct_answers;
    }

    // Close statements
    $stmt_exam_details->close();
    $stmt_subject_name->close();
    $stmt->close();
}
$conn->close();
?>

<!-- HTML for displaying the exam details -->
<div class="container mt-5 border rounded shadow p-0">
    <h3 class="text-white bg-primary p-2"><?= htmlspecialchars($subject_name) ?>
        <?= htmlspecialchars($exam_type) ?>-Term
    </h3>

    <!-- Summary Section -->
    <div class="mt-3 mx-2">
        <a href="exam_history.php" class="btn btn-secondary">Back</a>
    </div>
    <div class="alert-info mt-3 px-3 py-1">
        <p><strong>Total Marks:</strong> <?= htmlspecialchars($marks) ?></p>
        <p><strong>Total Questions:</strong> <?= htmlspecialchars($total_questions) ?></p>
        <p><strong>Correct Answers:</strong> <?= htmlspecialchars($correct_answers) ?></p>
        <p><strong>Wrong Answers:</strong> <?= htmlspecialchars($wrong_answers) ?></p>
    </div>

    <!-- Questions Table with Scroll -->
    <div class="p-3" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Question Text</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions_array as $question): ?>
                    <tr>
                        <td><?= htmlspecialchars($question['question_text']) ?></td>
                        <td>
                            <?php foreach ($question['options'] as $option): ?>
                                <?php if ($option['option_id'] == $question['selected_option_id']): ?>
                                    <span class="badge <?= $option['is_correct'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= htmlspecialchars($option['option_text']) ?>
                                        <?= $option['is_correct'] ? '✓' : '✗' ?>
                                    </span>
                                <?php else: ?>
                                    <span>
                                        <?= htmlspecialchars($option['option_text']) ?>
                                        <?= $option['is_correct'] ? '✓' : '' ?>
                                    </span>
                                <?php endif; ?>
                                <br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>