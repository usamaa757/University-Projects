<?php
// Include database connection file
include '../include_files/db_connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data   
        $course_id = $_POST['course_id'];
        $assignment_question = $_POST['assignment_question'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
      
            // Prepare SQL query to insert assignment details into the database
            $sql = "INSERT INTO assignments (course_id, assignment_question, start_date, end_date) VALUES (?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                // Bind parameters
                $stmt->bind_param('isss', $course_id, $assignment_question, $start_date, $end_date);

                // Execute the query
                if ($stmt->execute()) {

                    echo '<script>alert("Assignment uploaded and saved successfully."); window.location="upload_assignment.php?course_id=' . $course_id . '"; </script>)';
                } else {
               
                    echo '<script>
                alert("Error: ' . $stmt->error . '");
                window.location="upload_assignment.php?course_id=' . $course_id . '";
            </script>';
                }

                // Close statement
                $stmt->close();
            } else {

                echo '<script>
                alert("Error: ' . $conn->error . '");
                window.location="upload_assignment.php?course_id=' . $course_id . '";
            </script>';
            }

    // Close database connection
    $conn->close();
   
}
