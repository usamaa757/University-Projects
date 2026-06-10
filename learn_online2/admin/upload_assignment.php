<?php
include '../include_files/db_connection.php';
// Initialize variables
$course_name = "";
$course_id = "";

// Check if the course ID is provided via GET parameter
if (isset($_GET['course_id'])) {
    // Retrieve the course ID from GET parameter
    $course_id = $_GET['course_id'];

    // Fetch course details including class_id from the courses table
    $sql = "SELECT * FROM courses WHERE course_id = '$course_id'";

    // Execute the query
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch course details
        $row = $result->fetch_assoc();
        $course_name = $row['course_name'];
    } else {
        $course_name = "Course not found";
    }
} else {
    $course_name = "course ID not provided";
}
require('header.php');
?>


    <!-- Assignment Upload Form -->
    <div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h3><?php echo $course_name; ?></h3>
            <form action="upload_assignment_process.php" method="post" enctype="multipart/form-data">
                <div class="form-group col-md-6">
                    <label for="assignment">Choose Assignment File</label>
                    <textarea name="assignment_question" id="assignment_question" class="form-control" rows="5" required></textarea>

                </div>
                <div class="form-group col-md-4">
                    <label for="start_date">Start Date</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">End Date</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" required>
                </div>
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
</div>


    <!-- Bootstrap JS and dependencies -->
    <script src="../js/jquery-3.5.1.slim.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>