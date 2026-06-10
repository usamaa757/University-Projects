<?php
require('header.php');
session_start();

// Set the total time allowed for the quiz
$total_time_allowed = 60; // 1 minute

// Always reset the start time when the quiz page is loaded
$_SESSION['start_time'] = time();

if (!isset($_SESSION['student_email'])) {
    header("Location: student_login.php");
    exit();
}

include 'submit_quiz_process.php';
include '../include_files/db_connection.php';

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];

    // Fetch quiz details based on quiz_id
    $query_quiz = "SELECT * FROM quizzes WHERE quiz_id = ?";
    $stmt_quiz = $conn->prepare($query_quiz);
    $stmt_quiz->bind_param('i', $quiz_id);
    $stmt_quiz->execute();
    $result_quiz = $stmt_quiz->get_result();

    if ($result_quiz->num_rows > 0) {
        $quiz = $result_quiz->fetch_assoc();
    } else {
        echo "Quiz not found.";
        exit();
    }

    // Fetch questions and options for the quiz
    $query_questions = "SELECT q.question_id, q.question_text, o.option_id, o.option_text
                        FROM questions q
                        INNER JOIN options o ON q.question_id = o.question_id
                        WHERE q.quiz_id = ?";
    $stmt_questions = $conn->prepare($query_questions);
    $stmt_questions->bind_param('i', $quiz_id);
    $stmt_questions->execute();
    $result_questions = $stmt_questions->get_result();

    $questions = [];
    while ($row_question = $result_questions->fetch_assoc()) {
        $question_id = $row_question['question_id'];

        if (!isset($questions[$question_id])) {
            $questions[$question_id] = [
                'question_id' => $question_id,
                'question_text' => $row_question['question_text'],
                'options' => []
            ];
        }

        $questions[$question_id]['options'][] = [
            'option_id' => $row_question['option_id'],
            'option_text' => $row_question['option_text']
        ];
    }
} else {
    echo "Quiz ID not provided.";
    exit();
}
?>

<script>
    var totalSeconds = <?php echo $total_time_allowed; ?>;

    var timerInterval = setInterval(function() {
        totalSeconds--;
        sessionStorage.setItem('remainingSeconds', totalSeconds);

        if (totalSeconds <= 0) {
            clearInterval(timerInterval);
            sessionStorage.removeItem('remainingSeconds'); // Remove the remaining time from session storage
            window.location.href = 'timeout_page.php';
        } else {
            var minutes = Math.floor(totalSeconds / 60);
            var seconds = totalSeconds % 60;
            document.getElementById('timer').innerHTML = minutes + 'm ' + seconds + 's';
        }
    }, 1000);
</script>

<div class="container mt-5">
    <div class="row flex-container">
        <div class="col-md-6">
            <h3><?php echo $quiz['course_name']; ?></h3>
        </div>
        <div class="col-md-6">
            <h3 id="timer" class="text-right"></h3>
        </div>
    </div>

    <form action="submit_quiz_process.php?quiz_id=<?php echo $quiz_id; ?>" method="post">
        <table class="table">
            <thead>
                <tr>
                    <th>Question</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $question) : ?>
                    <tr>
                        <td>
                            <div><?php echo $question['question_text']; ?></div>
                            <ul class="list-unstyled">
                                <?php foreach ($question['options'] as $option) : ?>
                                    <li>
                                        <label class="form-check-label">
                                            <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $option['option_id']; ?>" class="form-check-input" required>
                                            <?php echo $option['option_text']; ?>
                                        </label>
                                    </li>
                                    <?php endforeach; ?>
                                    <input type="hidden" name="course_id" value="<?php echo $quiz['course_id']; ?>">
                                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="submit-btn text-center">
            <button type="submit" name="submit" class="btn btn-primary">Submit Quiz</button>
        </div>
    </form>
</div>
