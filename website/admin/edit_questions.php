<?php
include 'header.php'; // Assuming you have a header file
include '../db_connection.php'; // Include the database connection

// Check if the exam_id is passed in the URL
if (isset($_GET['exam_id']) && isset($_GET['subject_id']) && $_GET['exam_type']) {
    $exam_id = $_GET['exam_id'];
    $subject_id = $_GET['subject_id'];
    $exam_type = $_GET['exam_type'];
    $subject_id = $_GET['subject_id'];


    if ($exam_type === "Mid") {
        $table_name = "mid_exams";
        $questions_table = "mid_exam_questions";
        $options_table = "mid_exam_options";
    } elseif ($exam_type === "Final") {
        $table_name = "final_exams";
        $questions_table = "final_exam_questions";
        $options_table = "final_exam_options";
    } else {
        die("Invalid exam type.");
    }
    // Query to fetch questions for the given exam_id
    $sql = "SELECT question_id, question_text FROM $questions_table WHERE exam_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $exam_id, $subject_id);  // Bind the exam_id to the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any questions are found
    if ($result->num_rows > 0) {
        echo "<br><br><br><br><br>";
        echo "<div class='container'>";
        echo "<form method='POST' action='update_questions.php'>";

        echo "<div class='card'>";

        // Card header for title
        echo "<div class='card-header bg-primary '>";
        echo "<h3 class='card-title mb-0 text-white'>Edit Questions and Options for Exam ID: " . htmlspecialchars($exam_id) . "</h3>";
        echo "</div>";

        // Back to add questions button
        echo "<div class='card-body'>";
        echo "<a href='add_questions.php?exam_id=$exam_id&subject_id=$subject_id' class='btn btn-secondary mb-3'>Back to Add Questions</a>";

        // Display success or error messages if any
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        }

        // Scrollable table for questions
        echo "<div style='max-height: 400px; overflow-y: auto;'>"; // Fixed height and scrollable container
        echo "<table class='table table-bordered table-hover'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>
                <th>Question ID</th>
                <th>Question Text</th>
                <th>Options</th>
                <th>Action</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";

        // Loop through the questions and display them
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];

            echo "<tr>";

            // Display the question ID and question text fields
            echo "<td>" . htmlspecialchars($row['question_id']) . "</td>";
            echo "<td><input class='form-control' type='text' name='questions[" . $row['question_id'] . "]' value='" . htmlspecialchars($row['question_text']) . "'></td>";

            // Display the options for this question with radio buttons before option text fields
            echo "<td>";
            $option_sql = "SELECT option_id, option_text, is_correct FROM $options_table WHERE question_id = ?";
            $option_stmt = $conn->prepare($option_sql);
            $option_stmt->bind_param("i", $question_id);
            $option_stmt->execute();
            $option_result = $option_stmt->get_result();

            while ($option_row = $option_result->fetch_assoc()) {
                $option_id = $option_row['option_id'];
                $option_text = $option_row['option_text'];
                $is_correct = $option_row['is_correct'];
                $checked = $is_correct ? "checked" : "";

                echo "<div class='input-group mb-2'>";
                echo "<div class='input-group-prepend'>
                        <div class='input-group-text'>
                            <input class='form-check-input' type='radio' name='correct_option[" . $question_id . "]' value='" . $option_id . "' " . $checked . ">
                        </div>
                      </div>";
                echo "<input class='form-control' type='text' name='options[" . $option_id . "]' value='" . htmlspecialchars($option_text) . "'>";
                echo "</div>";
            }
            echo "</td>";


            echo "<td>";
            echo "<a href='delete_question.php?question_id=" . $row['question_id'] . "&exam_id=" . $exam_id . "&subject_id=" . $subject_id . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Do you really want to delete this question?');\">Delete</a>";
            echo "</td>";

            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>"; // End scrollable table

        echo "</div>"; // End card-body

        // Fixed position for the update button
        echo "<div class='text-center mt-4'>";
        echo "<input type='hidden' name='exam_id' value='" . htmlspecialchars($exam_id) . "'>";
        echo "<input type='hidden' name='subject_id' value='" . htmlspecialchars($subject_id) . "'>";
        echo "<input type='hidden' name='exam_type' value='" . htmlspecialchars($exam_type) . "'>";
        echo "<button type='submit' name='update_questions' class='btn btn-success'>Update Questions</button>";
        echo "</div>"; // End update button container

        echo "</div>"; // End card
        echo "</form>";
        echo "</div>"; // End container
    } else {
        echo "<div class='container mt-5'>";
        echo "<div class='alert alert-warning'>No questions found for this exam.</div>";
        echo "</div>";
    }

    $stmt->close(); // Close the statement
} else {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>No exam selected.</div>";
    echo "</div>";
}
?>