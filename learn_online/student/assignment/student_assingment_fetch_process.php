<?php

include_once '../../db_connection.php';


$assignments = array();

// Check if the assignment ID and subject ID are provided in the GET method
if (isset($_GET['assignment_id']) && isset($_GET['course_id'])) {
    // Retrieve the assignment ID and subject ID from the GET method
    $assignment_id = $_GET['assignment_id'];
    $course_id = $_GET['course_id'];

    // Fetch the assignment details based on the assignment ID and subject ID
    $query_fetch_assignment = "  SELECT a.*, c.course_name 
    FROM assignments a 
    INNER JOIN courses c ON a.course_id = c.course_id 
    WHERE a.assignment_id = ? AND a.course_id = ?";
    $statement_fetch_assignment = $conn->prepare($query_fetch_assignment);
    $statement_fetch_assignment->bind_param('ii', $assignment_id, $course_id);
    $statement_fetch_assignment->execute();
    $result_fetch_assignment = $statement_fetch_assignment->get_result();

    if ($result_fetch_assignment->num_rows > 0) {
        // Assignment found, display or process it accordingly
        while ($row = $result_fetch_assignment->fetch_assoc()) {
            $assignments[] = $row;
          
        }
    } else {
        // Assignment not found
        echo "Assignment not found.";
    }

    $statement_fetch_assignment->close();

} else {
    echo "Assignment ID not provided.";
}
?>