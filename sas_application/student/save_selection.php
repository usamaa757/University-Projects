<?php
session_start();

// Include the database connection
include '../other/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $student_id = $_SESSION['user_id'];
    $selected_courses = $_POST['selected_courses'] ?? [];
    $class_id = $_POST['class_id']; // Retrieve the class ID from the form

    // Verify that the student_id exists in the students table
    $check_query = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo "Invalid student ID.";
    } else {
        // Retrieve the current selections for the student
        $existing_selections = [];
        $existing_selections_query = "SELECT course_id FROM course_selection WHERE student_id = ?";
        $stmt = $conn->prepare($existing_selections_query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $existing_selections_result = $stmt->get_result();
        while ($row = $existing_selections_result->fetch_assoc()) {
            $existing_selections[] = $row['course_id'];
        }

        // Process selected courses
        foreach ($selected_courses as $course_id) {
            // If the course is already selected, continue to the next iteration
            if (in_array($course_id, $existing_selections)) {
                continue;
            }

            // Insert the new selection
            $insert_query = "INSERT INTO course_selection (student_id, class_id, course_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iii", $student_id, $class_id, $course_id);
            $stmt->execute();
        }

        // Process deselected courses
        foreach ($existing_selections as $existing_course_id) {
            // If the course is still selected, continue to the next iteration
            if (in_array($existing_course_id, $selected_courses)) {
                continue;
            }

            // Delete the deselected course
            $delete_query = "DELETE FROM course_selection WHERE student_id = ? AND course_id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param("ii", $student_id, $existing_course_id);
            $stmt->execute();
        }

        echo "Course selection updated successfully!";
    }

    $stmt->close();
}

$conn->close();
?>
