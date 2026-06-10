<?php


include '../common_process/db_connection.php';

// Initialize an empty array to store subjects
$subjects = [];

// Check if class_id is set in session
if (isset($_SESSION['class_id'])) {
    // Retrieve class_id from session
    $class_id = $_SESSION['class_id'];
  

    // Fetch class name based on class_id
    $sql = "SELECT class_name FROM class WHERE class_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch class name data
    $class_name = '';
  
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $class_name = $row['class_name'];
    }
    // Use prepared statement to fetch subjects based on class_id
    $stmt_subjects = $conn->prepare("SELECT s.subject_id, s.subject_name
    FROM class_subjects cs
    JOIN subjects s ON cs.subject_id = s.subject_id
    WHERE cs.class_id = ?");
    $stmt_subjects->bind_param("i", $class_id);
    $stmt_subjects->execute();
    $result_subjects = $stmt_subjects->get_result();

    // Check if there was an error with the SQL statement
    if (!$stmt_subjects) {
        // SQL statement execution failed
        die("Error executing the SQL statement: " . $conn->error);
    }

    // Check if there are subjects available for the selected class
    if ($result_subjects->num_rows > 0) {
        // Fetch subjects and store them in the array
        $subjects = $result_subjects->fetch_all(MYSQLI_ASSOC);
    } else {
        // No subjects available for the selected class
        $subjects[] = ["id" => null, "name" => "No subjects available for the selected class."];
    }

    // Close the prepared statement
    $stmt_subjects->close();
} else {
    // class_id is not set in session
    $subjects[] = ["id" => null, "name" => "class_id is not set in session."];
}

// Close the database connection
$conn->close();
?>