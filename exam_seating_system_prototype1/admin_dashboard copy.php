<?php
include 'db.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

// Fetch total rooms
$room_result = mysqli_query($conn, "SELECT COUNT(*) AS total_rooms FROM rooms");
$room_data = mysqli_fetch_assoc($room_result);
$total_rooms = $room_data['total_rooms'];

// Fetch upcoming exams
$exam_result = mysqli_query($conn, "
    SELECT es.exam_id, c.course_name, es.exam_date 
    FROM exam_schedule es
    JOIN courses c ON es.course_id = c.course_id
    ORDER BY es.exam_date ASC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Seating System</title>
    <style>


    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a class="brand" href="index.php">Exam Seating System</a>
            <ul class="nav-links">
                <?php if ($admin_id) { ?>
                <li><a href="add_data.php">Add Data</a></li>
                <li><a href="assign_exam.php">Assign Exams</a></li>
                <li><a href="seating.php">Assign Seats</a></li>
                <li><a href="view_seating.php">View Seating Plan</a></li>
                <li><a href="logout.php">Logout</a></li>
                <?php } else { ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to the Exam Seating System</h1>
        <p>Manage student seating efficiently and ensure fair allocation of seats.</p>

        <div class="info-box">
            <h2>Total Exam Rooms Available</h2>
            <p><strong><?php echo $total_rooms; ?></strong> Rooms Available</p>
        </div>

        <h2>Upcoming Exams</h2>
        <table>
            <tr>
                <th>Exam ID</th>
                <th>Course Name</th>
                <th>Exam Date</th>
            </tr>
            <?php while ($exam = mysqli_fetch_assoc($exam_result)) { ?>
            <tr>
                <td><?php echo $exam['exam_id']; ?></td>
                <td><?php echo $exam['course_name']; ?></td>
                <td><?php echo date("d M, Y", strtotime($exam['exam_date'])); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>

</html>