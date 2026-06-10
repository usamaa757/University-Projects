<?php
include "header.php";
include "../db_connection.php"; // Include database connection

// Fetch all subjects from the database
$query = "SELECT subject_id, subject_name FROM subjects";
$result = $conn->query($query);

// Handle form submission to create an exam
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = $_POST['subject_id'];
    $exam_type = $_POST['exam_type'];

    // Choose the correct table based on exam type
    $table_name = ($exam_type === 'Mid') ? 'mid_exams' : 'final_exams';

    // Insert into the correct table
    $insertQuery = "INSERT INTO $table_name (subject_id, exam_type) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("is", $subject_id, $exam_type);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Exam created successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

// Fetch created exams from both tables
$examsQuery = "SELECT me.exam_id, s.subject_name, me.exam_type, me.date, me.subject_id
               FROM mid_exams me 
               JOIN subjects s ON me.subject_id = s.subject_id
               UNION ALL
               SELECT fe.exam_id, s.subject_name, fe.exam_type, fe.date, fe.subject_id
               FROM final_exams fe
               JOIN subjects s ON fe.subject_id = s.subject_id";
$stmt = $conn->prepare($examsQuery);
$stmt->execute();
$examsResult = $stmt->get_result();
?>
<br><br><br><br><br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0 text-white">Create Exam</h3>
                </div>
                <div class="card-body">

                    <!-- Success or Error Message -->
                    <?php
                    if (isset($_SESSION['success'])) {
                        echo '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
                        unset($_SESSION['success']);
                    }
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                    ?>

                    <form action="" method="post">
                        <div class="form-group">
                            <label for="subject_id">Select Subject</label>
                            <select class="form-control" name="subject_id" id="subject_id" required>
                                <option value="">-- Select Subject --</option>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['subject_id'] . '">' . htmlspecialchars($row['subject_name']) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No subjects available</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="exam_type">Select Exam Type</label>
                            <select class="form-control" name="exam_type" id="exam_type" required>
                                <option value="Mid">Midterm</option>
                                <option value="Final">Final Term</option>
                            </select>
                        </div>

                        <div class="form-group text-center mt-2">
                            <button type="submit" class="btn btn-primary btn-block">Create Exam</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Display Created Exams -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="mb-0 text-white">Created Exams</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Exam ID</th>
                                <th>Subject Name</th>
                                <th>Exam Type</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($examsResult->num_rows > 0): ?>
                                <?php while ($exam = $examsResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($exam['exam_id']); ?></td>
                                        <td><?php echo htmlspecialchars($exam['subject_name']); ?></td>
                                        <td><?php echo htmlspecialchars($exam['exam_type']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d M, Y', strtotime($exam['date']))); ?></td>
                                        <td>
                                            <a href="add_questions.php?exam_id=<?php echo $exam['exam_id']; ?>&subject_id=<?php echo $exam['subject_id']; ?>"
                                                class="btn btn-primary btn-sm">Add Question</a>
                                            <a href="delete_exam.php?exam_id=<?php echo $exam['exam_id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this exam?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No exams created yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>