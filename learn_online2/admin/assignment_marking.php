<?php

include '../include_files/db_connection.php';
$assignments = [];
if (isset($_GET['assignment_id']) && isset($_GET['student_id'])) {
    $assignment_id = $_GET['assignment_id'];
    $student_id = $_GET['student_id'];

    $fetch_assignments_query = "SELECT sa.*, c.course_name
    FROM student_assignment sa 
    INNER JOIN courses c ON sa.course_id = c.course_id
    WHERE sa.assignment_id = ? AND student_id = ? ";
    $stmt = $conn->prepare($fetch_assignments_query);
    $stmt->bind_param("ii", $assignment_id, $student_id);
    $stmt->execute();
    $assignments_result = $stmt->get_result();

    if ($assignments_result->num_rows > 0) {
        while ($row = $assignments_result->fetch_assoc()) {
            $assignments[] = $row;
        }
    } else {
        $errorMsg = "No assignments found for marking.";
    }
} else {
    $errorMsg = "No assignment ID found.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $assignment_id = $_POST['assignment_id'];
    $marks = $_POST['marks'];

    // Validate input
    if (empty($student_id) || empty($assignment_id) || empty($marks)) {
        $errorMsg = "All fields are required.";
    } else {
        // Update the database
        $sql = "UPDATE student_assignment SET marks = ? WHERE student_id = ? AND assignment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $marks, $student_id, $assignment_id);

        if ($stmt->execute()) {
            $resultMsg = "Marks added successfully.";
        } else {
            $errorMsg = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
require('header.php');
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div>
                <a href="assignment_list.php?assignment_id=<?php echo $assignment_id;?>" class="btn btn-primary">
                    Back
                </a>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($assignments)) : ?>
                <h3><?php echo $assignments[0]['course_name']; ?></h3>
            <?php endif; ?>
            <?php
            $sno = 1;
            foreach ($assignments as $assignment) {
            ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <?php
                        if (!empty($errorMsg)) {
                            echo "<div class='alert alert-danger'>$errorMsg</div>";
                        } elseif (!empty($resultMsg)) {
                            echo "<div class='alert alert-success'>$resultMsg</div>";
                        }
                        ?>
                        <p><strong>Student ID:</strong> <?php echo $assignment["student_id"]; ?></p>
                        <p><strong>Assignment Answer:</strong> <?php echo $assignment["assignment_answer"]; ?></p>
                        <form method='POST' action='assignment_marking.php?assignment_id=<?php echo $assignment['assignment_id'] . '&student_id=' . $assignment['student_id']; ?>' class="mb-3">
                            <input type='hidden' name='assignment_id' value='<?php echo $assignment["assignment_id"] ?>'>
                            <input type='hidden' name='student_id' value='<?php echo $assignment["student_id"] ?>'>
                            <div class="form-group">
                                <label for='marks'>Marks:</label>
                                <input type='number' name='marks' id="marks" value='<?php echo $assignment["marks"]; ?>' class="form-control" required>
                            </div>
                            <button type='submit' class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
