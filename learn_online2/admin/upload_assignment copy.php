<?php
include '../include_files/db_connection.php';

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Retrieve the course_id and course_name using INNER JOIN
    $stmt_course = $conn->prepare("SELECT *  FROM courses  WHERE course_id = ?");

    $stmt_course->bind_param("i", $course_id);
    $stmt_course->execute();
    $result = $stmt_course->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $course_id = $row['course_id'];
        $course_name = $row['course_name'];
    } else {
        // course not found
        $course_id = null;
        $course_name = "course Not Found";
    }
    $stmt_course->close();
} else {
    // No course_id provided in URL
    $course_id = null;
    $course_name = "No course ID Provided";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_id = $_GET['course_id']; // Assuming you are passing course_id via URL parameter
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    

    // Prepare insert statement for quiz
    $insert_quiz = $conn->prepare("INSERT INTO quizzes (course_id, course_name, start_date, end_date) VALUES  (?, ?, ?, ?)");
    $insert_quiz->bind_param("isss", $course_id, $course_name, $start_date, $end_date);
    $insert_quiz->execute();
    $resultMsg  = "Quiz created successfully";

    // Get the inserted quiz_id
    $quiz_id = $insert_quiz->insert_id;
}


require('header.php');
?>


    <!-- Assignment Upload Form -->
    <div class="container mt-5">
    <div class="row">
        <div class="col-lg-12">
            <h3><?php echo $course_name; ?></h3>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="result-output">
                        <?php
                        if (!empty($errorMsg)) {
                            echo "<span class='text-danger'>$errorMsg</span>";
                        } elseif (!empty($resultMsg)) {
                            echo "<span class='text-success'>$resultMsg</span>";
                        }
                        ?>
                    </div>
                    <h2>Create Quiz</h2>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?course_id=' . $_GET['course_id']; ?>" method="post">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="start_date">
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="end_date">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" name="submit">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h2>Add Questions</h2>
                    <?php
                    // Retrieve all quizzes from the database based on course_id
                    $course_id = $_GET['course_id']; // Assuming you are passing course_id via URL parameter
                    $sql = "SELECT * FROM quizzes WHERE course_id = $course_id ORDER BY quiz_id";
                    $result = $conn->query($sql);

                    // Display a list of quizzes with links to add questions
                    if ($result->num_rows > 0) {
                        $sn = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo '<a class="btn btn-outline-primary mb-2 d-block" href="add_question.php?quiz_id=' . $row['quiz_id'] .'">Quiz ' . $sn++ . '</a>';
                        }
                    } else {
                        echo "<p>No quizzes found for this course.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>



    <!-- Bootstrap JS and dependencies -->
    <script src="../js/jquery-3.5.1.slim.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>