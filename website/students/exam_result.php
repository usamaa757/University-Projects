<?php
include 'header.php';
include '../db_connection.php';

$student_exam_id = isset($_GET['student_exam_id']) ? $_GET['student_exam_id'] : $_SESSION['student_exam_id'];
$exam_type = isset($_GET['exam_type']) ? $_GET['exam_type'] : null; // Ensure to fetch exam_type correctly
$correct_count = 0;
$total_questions = 0;
$attempted_questions = 0; // Initialize variable for attempted questions

// Determine table names based on exam type
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

// Fetch exam details using exam_type and student_exam_id
$sql_exam_details = "
    SELECT e.exam_id, e.exam_type,se.marks
    FROM $table_name e
    JOIN student_exam se ON e.exam_id = se.exam_id
    WHERE se.student_exam_id = ?";

$stmt_exam_details = $conn->prepare($sql_exam_details);
$stmt_exam_details->bind_param("i", $student_exam_id);
$stmt_exam_details->execute();
$result_exam_details = $stmt_exam_details->get_result();

$exam_details = [];
if ($row_exam = $result_exam_details->fetch_assoc()) {
    $exam_details = [
        'exam_id' => $row_exam['exam_id'],
        'exam_type' => $row_exam['exam_type'],
        'marks' => $row_exam['marks']
    ];
}

// Fetch total questions for the determined exam type
$sql_total_questions = "
    SELECT COUNT(q.question_id) AS total_questions
    FROM $questions_table q
    WHERE q.exam_id = ?";

$stmt_total_questions = $conn->prepare($sql_total_questions);
$stmt_total_questions->bind_param("i", $exam_details['exam_id']);
$stmt_total_questions->execute();
$result_total_questions = $stmt_total_questions->get_result();

if ($row_total = $result_total_questions->fetch_assoc()) {
    $total_questions = $row_total['total_questions'];
}

// Fetch total attempted questions
$sql_attempted_questions = "
    SELECT COUNT(DISTINCT a.question_id) AS attempted_questions
    FROM student_exam_answers a
    WHERE a.student_exam_id = ?";

$stmt_attempted_questions = $conn->prepare($sql_attempted_questions);
$stmt_attempted_questions->bind_param("i", $student_exam_id);
$stmt_attempted_questions->execute();
$result_attempted_questions = $stmt_attempted_questions->get_result();

if ($row_attempted = $result_attempted_questions->fetch_assoc()) {
    $attempted_questions = $row_attempted['attempted_questions'];
}

// Now fetch all student answers along with correct answer verification
$sql_answers = "
    SELECT a.question_id, a.selected_option_id, o.is_correct
    FROM student_exam_answers a
    JOIN $options_table o ON a.selected_option_id = o.option_id
    WHERE a.student_exam_id = ?";

$stmt_answers = $conn->prepare($sql_answers);
$stmt_answers->bind_param("i", $student_exam_id);
$stmt_answers->execute();
$result_answers = $stmt_answers->get_result();

// Check each student-submitted answer for correctness
while ($row = $result_answers->fetch_assoc()) {
    if ($row['is_correct'] == 1) {
        $correct_count++;
    }
}

// Calculate wrong answers by subtracting correct answers from attempted questions
$wrong_count = $attempted_questions - $correct_count;

// Close statements and connection
$stmt_exam_details->close();
$stmt_total_questions->close();
$stmt_attempted_questions->close();
$stmt_answers->close();
$conn->close();

// Prepare data for the chart
$data = [
    'labels' => ['Correct Answers', 'Wrong Answers'],
    'data' => [$correct_count, $wrong_count],
];
?>
<br><br><br><br><br>
<div class="container">
    <h3>Exam Results</h3>

    <div class="alert alert-info">
        <strong>Exam Type:</strong> <?php echo htmlspecialchars($exam_type); ?><br>
        <strong>Total Questions Attempted:</strong> <?php echo $attempted_questions; ?><br>
        <strong>Correct Answers:</strong> <?php echo $correct_count; ?><br>
        <strong>Wrong Answers:</strong> <?php echo $wrong_count; ?><br>
    </div>

    <!-- Limit the width of the canvas -->
    <div style="max-width: 1000px; margin: 0 auto;">
        <canvas id="resultsChart" width="700" height="500"></canvas>
    </div>

    <!-- <div class="my-2">
        <form id="resetForm" action="reset_exam.php" method="post">
            <input type="hidden" name="exam_id" value="<?php echo $exam_details['exam_id']; ?>">
            <input type="hidden" name="student_exam_id" value="<?php echo $student_exam_id; ?>">
            <input type="hidden" name="correct_count" value="<?php echo $correct_count; ?>">
            <input type="hidden" name="wrong_count" value="<?php echo $wrong_count; ?>">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            <button type="button" class="btn btn-danger" onclick="confirmReset()">Reset Exam</button>
        </form>
    </div> -->

    <!-- <div class="my-2">
        <form id="saveForm" action="save_results.php" method="post">
            <input type="hidden" name="exam_id" value="<?php echo $exam_details['exam_id']; ?>">
            <input type="hidden" name="student_exam_id" value="<?php echo $student_exam_id; ?>">
            <input type="hidden" name="correct_count" value="<?php echo $correct_count; ?>">
            <input type="hidden" name="wrong_count" value="<?php echo $wrong_count; ?>">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            <button type="submit" class="btn btn-success">Save Results</button>
        </form>
    </div> -->

</div>

<script>
    function confirmReset() {
        const saveResult = confirm("Please save your result before resetting the exam.");
        if (saveResult) {
            // If user confirms, submit the reset form
            document.getElementById('resetForm').submit();
        } else {
            // If user cancels, simply do nothing
            return; // No need to submit
        }
    }

    const ctx = document.getElementById('resultsChart').getContext('2d');
    const resultsChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($data['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($data['data']); ?>,
                backgroundColor: ['#28a745', '#dc3545'],
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Marks Distribution'
                }
            }
        }
    });
</script>
</body>

</html>