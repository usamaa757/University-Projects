<?php
include '../other/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id']) && isset($_POST['marks']) && isset($_POST['class_id'])) {
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $marks = $_POST['marks'];

    $success = true;

    foreach ($marks as $course_id => $mark) {
        // Insert the marks along with class_id into the results table
        $sql = "INSERT INTO results (student_id, class_id, course_id, marks) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE marks = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiii", $student_id, $class_id, $course_id, $mark, $mark);
        if (!$stmt->execute()) {
            $success = false;
            break; // Stop processing if there is an error
        }
        $stmt->close();
    }

    $conn->close();

    if ($success) {
        header("Location: degree_dmc.php?status=success");
    } else {
        header("Location: degree_dmc.php?status=error");
    }
    exit;
} else {
    header("Location: degree_dmc.php?status=invalid");
    exit;
}
?>
