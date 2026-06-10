<?php
include 'db.php';
include 'header.php';

if (isset($_POST['add_student'])) {
    $name = $_POST['student_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if email already exists
    $check_email = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");

    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Error: Email already exists!');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('Error: Passwords do not match!');</script>";
    } else {
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        mysqli_query($conn, "INSERT INTO students (student_name, email, password) 
                             VALUES ('$name', '$email', '$hashed_password')");

        // Success message
        echo "<script>alert('Student added successfully!'); window.location.href='add_data.php';</script>";
    }
}


// Add Course
if (isset($_POST['add_course'])) {
    $course = $_POST['course_name'];
    mysqli_query($conn, "INSERT INTO courses (course_name) VALUES ('$course')");
    echo "<script>alert('Course added successfully!'); window.location.href='add_data.php';</script>";
}

// Add Exam Schedule
if (isset($_POST['add_exam'])) {
    $course_ids = $_POST['course_id'];
    $exam_date = $_POST['exam_date'];
    $exam_time = $_POST['exam_time'];

    if (!empty($course_ids)) {
        foreach ($course_ids as $course_id) {
            mysqli_query($conn, "INSERT INTO exam_schedule (course_id, exam_date, exam_time) VALUES ('$course_id', '$exam_date', '$exam_time')");
        }
        echo "<script>alert('Exam scheduled successfully!'); window.location.href='add_data.php';</script>";
    } else {
        echo "<script>alert('Error: Please select at least one course!');</script>";
    }
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">

        <!-- Add Student -->
        <div class="col-md-4">
            <div class="card shadow-lg p-4 mb-4">
                <h3 class="text-center mb-3">Add Student</h3>
                <form action="" method="POST">
                    <div class="mb-3">
                        <input type="text" name="student_name" class="form-control" placeholder="Student Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="confirm_password" class="form-control"
                            placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" name="add_student" class="btn btn-dark w-100">Add Student</button>
                </form>
            </div>
        </div>

        <!-- Add Course -->
        <div class="col-md-4">
            <div class="card shadow-lg p-4 mb-4">
                <h3 class="text-center mb-3">Add Course</h3>
                <form action="" method="POST">
                    <div class="mb-3">
                        <input type="text" name="course_name" class="form-control" placeholder="Course Name" required>
                    </div>
                    <button type="submit" name="add_course" class="btn btn-dark w-100">Add Course</button>
                </form>
            </div>
        </div>

        <!-- Add Exam Schedule -->
        <div class="col-md-4">
            <div class="card shadow-lg p-4">
                <h3 class="text-center mb-3">Add Exam Schedule</h3>
                <form action="" method="POST">
                    <div class="mb-3">
                        <select name="course_id[]" multiple class="form-select">
                            <?php
                            include 'db.php';
                            $result = mysqli_query($conn, "SELECT * FROM courses");
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['course_id'] . "'>" . $row['course_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="date" name="exam_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <input type="time" name="exam_time" class="form-control" required>
                    </div>
                    <button type="submit" name="add_exam" class="btn btn-dark w-100">Add Exam</button>
                </form>
            </div>
        </div>

    </div>
</div>

</body>

</html>