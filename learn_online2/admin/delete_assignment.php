<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

// Check if assignment ID is provided
if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    // Delete assignment
    $delete_stmt = $conn->prepare("DELETE FROM assignments WHERE assignment_id = ?");
    $delete_stmt->bind_param("i", $assignment_id);

    if ($delete_stmt->execute()) {
        $resultMsg = "Assignment deleted successfully.";
    } else {
        $errorMsg = "Error deleting assignment: " . $conn->error;
    }

    $delete_stmt->close();
} else {
    $errorMsg = "Assignment ID not provided.";
}

// Close connection
$conn->close();
header("Location: assignment_record.php?resultMsg=" . urlencode($resultMsg) . "&errorMsg=" . urlencode($errorMsg));

?>

<div class="result-output">
    <?php
    echo "<span id='error'>$errorMsg</span>";
    echo "<span id='result'>$resultMsg</span>";
    ?>
</div>
