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

<div class="data-container">

    <h3>Add Student</h3>
    <form action="" method="POST">
        <input type="text" name="student_name" placeholder="Student Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit" name="add_student">Add Student</button>
    </form>

    <h3>Add Course</h3>
    <form action="" method="POST">
        <input type="text" name="course_name" placeholder="Course Name" required>
        <button type="submit" name="add_course">Add Course</button>
    </form>

    <h3>Add Exam Schedule</h3>
    <form action="" method="POST">
        <select name="course_id[]" multiple>
            <?php
            include 'db.php';
            $result = mysqli_query($conn, "SELECT * FROM courses");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['course_id'] . "'>" . $row['course_name'] . "</option>";
            }
            ?>
        </select>
        <input type="date" name="exam_date" required>
        <input type="time" name="exam_time" required>
        <button type="submit" name="add_exam">Add Exam</button>
    </form>
</div>
</body>

</html>