<?php
include 'header.php';
include '../db_connection.php'; // Include database connection

// Check if subject_id is provided in the URL
if (!isset($_GET['subject_id'])) {
    echo "Subject ID is missing.";
    exit;
}

$subject_id = $_GET['subject_id'];

// Fetch the exam type and exam ID based on subject_id
$sql_exam = "SELECT exam_type, exam_id FROM mid_exams WHERE subject_id = ?";
$stmt_exam = $conn->prepare($sql_exam);
$stmt_exam->bind_param("i", $subject_id);
$stmt_exam->execute();
$result_exam = $stmt_exam->get_result();

if ($result_exam->num_rows === 0) {
    echo "No exam found for this subject.";
    exit;
}

$exam = $result_exam->fetch_assoc();
$exam_id = $exam['exam_id'];
$exam_type = strtolower($exam['exam_type']); // Convert to lowercase for easier comparison

// Fetch the subject name based on subject_id
$sql_subject = "SELECT subject_name FROM subjects WHERE subject_id = ?";
$stmt_subject = $conn->prepare($sql_subject);
$stmt_subject->bind_param("i", $subject_id);
$stmt_subject->execute();
$result_subject = $stmt_subject->get_result();

if ($result_subject->num_rows === 0) {
    echo "No subject found for this ID.";
    exit;
}

$subject = $result_subject->fetch_assoc();
$subject_name = $subject['subject_name']; // Get the subject_name

$stmt_exam->close(); // Close the statement for exams
$stmt_subject->close(); // Close the statement for subjects
$conn->close(); // Close the database connection
?>
<br><br><br><br><br>
<div class="container border rounded shadow p-0">
    <div class="text-center mb-4">
        <h3 class="bg-primary text-white p-3">
            <?php echo htmlspecialchars(ucfirst($exam_type)); ?>-Term Exam for
            <?php echo htmlspecialchars($subject_name); ?>
        </h3>
    </div>
    <div class="p-4">
        <div class="alert alert-info text-center">
            <p>
                You are about to take the <?php echo htmlspecialchars($exam_type); ?>-term exam for the selected
                subject.
                Please click the button below to begin.
            </p>
        </div>

        <!-- Display the Take Exam button with both subject_id and exam_id -->
        <div class="text-center">
            <a href="take_<?php echo $exam_type; ?>_exam.php?subject_id=<?php echo $subject_id; ?>&exam_id=<?php echo $exam_id; ?>"
                class="btn btn-primary btn-lg">
                Take Exam
            </a>
        </div>
    </div>
</div>

</body>

</html>