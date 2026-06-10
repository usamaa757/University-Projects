<?php
require '../other/db_connection.php';

function createAssignment($mysqli, $title, $description, $due_date) {
    $stmt = $mysqli->prepare("INSERT INTO assignments (title, description, due_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $due_date);
    $stmt->execute();
    $stmt->close();
}

function createQuiz($mysqli, $title, $questions) {
    $questions_json = json_encode($questions);
    $stmt = $mysqli->prepare("INSERT INTO quizzes (title, questions) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $questions_json);
    $stmt->execute();
    $stmt->close();
}

function createLecture($mysqli, $title, $content) {
    $stmt = $mysqli->prepare("INSERT INTO lectures (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();
}

function markAttendance($mysqli, $student_id, $course_id) {
    $stmt = $mysqli->prepare("INSERT INTO attendance (student_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $stmt->close();
}

function getLectures($mysqli) {
    $result = $mysqli->query("SELECT * FROM lectures");
    $lectures = [];
    while ($row = $result->fetch_assoc()) {
        $lectures[] = $row;
    }
    return $lectures;
}
