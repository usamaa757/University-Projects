<?php
session_start();
require('header.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../../login/student_login.php");
    exit();
}

include_once '../include_files/db_connection.php';

$course_name = ''; // Initialize course_name variable

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
}
$student_id = $_SESSION['student_id'];

// Query to get the course details for the student
$query_course = "SELECT sc.*, c.course_name 
                 FROM student_course sc 
                 INNER JOIN courses c ON sc.course_id = c.course_id 
                 WHERE sc.student_id = ?";
$stmt_course = $conn->prepare($query_course);
$stmt_course->bind_param('i', $student_id);
$stmt_course->execute();
$result_course = $stmt_course->get_result();

if ($result_course->num_rows > 0) {
    $row_course = $result_course->fetch_assoc();
    $course_id = $row_course['course_id'];
    $course_name = $row_course['course_name'];
} else {
    echo "<script>
    alert('No course found!');
    window.location.href = 'course_list.php'; // Redirect to course list or another appropriate page
    </script>";
    exit();
}

// Query to get quizzes for the course
$quizzes = [];
$query_quizzes = "SELECT * FROM quizzes WHERE course_id = ?";
$stmt_quizzes = $conn->prepare($query_quizzes);
$stmt_quizzes->bind_param('i', $course_id);
$stmt_quizzes->execute();
$result_quizzes = $stmt_quizzes->get_result();

if ($result_quizzes->num_rows > 0) {
    while ($row_quiz = $result_quizzes->fetch_assoc()) {
        // Check the submission status for the quiz
        $query_status = "SELECT status FROM student_quiz WHERE quiz_id = ? AND student_id = ?";
        $stmt_status = $conn->prepare($query_status);
        $stmt_status->bind_param('ii', $row_quiz['quiz_id'], $student_id);
        $stmt_status->execute();
        $result_status = $stmt_status->get_result();
        $status = 'not submitted';

        if ($result_status->num_rows > 0) {
            $row_status = $result_status->fetch_assoc();
            $status = $row_status['status'];
        }

        $row_quiz['status'] = $status;
        $quizzes[] = $row_quiz;
    }
} else {
    echo "<script>
    alert('No quiz found for the selected course!');
    window.location.href = 'course_list.php'; // Redirect to course list or another appropriate page
    </script>";
    exit();
}

$conn->close();
?>

<div class="container mt-5">
    <div class="row flex-container">
        <div class="col-md-6">
            <h3><?php echo htmlspecialchars($course_name); ?></h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sno = 1;
                    foreach ($quizzes as $quiz) : ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td>
                                <?php if ($quiz['status'] == 'submitted') : ?>
                                    <span class="badge badge-success disabled">Submitted</span>
                                <?php else : ?>
                                    <a href="submit_quiz.php?quiz_id=<?php echo $quiz['quiz_id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-primary">Take Quiz</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
