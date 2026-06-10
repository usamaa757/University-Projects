<?php
include 'db.php';
include 'header.php';
include 'admin_auth.php';
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
// Handle CSV upload
if (isset($_POST['upload_csv'])) {
    if ($_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");

        $headerSkipped = false;
        $successCount = 0;
        $errorCount = 0;
        $duplicateCount = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($data) < 3) {
                $errorCount++;
                continue;
            }

            $student_name = trim($data[0]);
            $email = trim($data[1]);
            $raw_password = trim($data[2]);
            $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

            if (!empty($student_name) && !empty($email)) {
                $check = mysqli_query($conn, "SELECT * FROM students WHERE email = '$email'");
                if (mysqli_num_rows($check) > 0) {
                    $duplicateCount++;
                    continue;
                }

                $stmt = $conn->prepare("INSERT INTO students (student_name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $student_name, $email, $hashed_password);

                if ($stmt->execute()) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
        }
    }

    fclose($handle);
    $summaryMessage = "Upload Summary:\\n";
    $summaryMessage .= "$successCount record(s) successfully added.\\n";
    $summaryMessage .= "$errorCount record(s) failed.\\n";
    $summaryMessage .= "$duplicateCount duplicate record(s) skipped.";

    echo "<script>alert('$summaryMessage'); window.location.href='add_data.php';</script>";
}

// Add Course
if (isset($_POST['add_course'])) {
    $course = $_POST['course_name'];
    mysqli_query($conn, "INSERT INTO courses (course_name) VALUES ('$course')");
    echo "<script>alert('Course added successfully!'); window.location.href='add_data.php';</script>";
}

// Add Room
if (isset($_POST['add_room'])) {
    $room_name = $_POST['room_name'];
    $total_seats = $_POST['total_seats'];
    $available_seats = $total_seats;
    mysqli_query($conn, "INSERT INTO rooms (room_name, total_seats, available_seats) VALUES ('$room_name', '$total_seats', '$available_seats')");
    echo "<script>alert('Room added successfully!'); window.location.href='add_data.php';</script>";
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
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Select CSV File:</label>
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit" name="upload_csv">Upload</button>
    </form>
    <h3>Add Course</h3>
    <form action="" method="POST">
        <input type="text" name="course_name" placeholder="Course Name" required>
        <button type="submit" name="add_course">Add Course</button>
    </form>


    <h3>Add Room</h3>
    <form action="" method="POST">
        <input type="text" name="room_name" placeholder="Room Name" required>
        <input type="number" name="total_seats" placeholder="Total Seats" required>
        <button type="submit" name="add_room">Add Room</button>
    </form>

</div>
</body>

</html>