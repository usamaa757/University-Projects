<?php
include 'db.php';

// Get available rooms
$room_query = mysqli_query($conn, "SELECT * FROM rooms WHERE available_seats > 0 ORDER BY room_id LIMIT 1");
$room = mysqli_fetch_assoc($room_query);

if (!$room) {
    die("<script>alert('No available rooms!'); window.location.href='assign_seat.php';</script>");
}

$room_id = $room['room_id'];
$total_seats = $room['total_seats'];
$available_seats = $room['available_seats'];

// Fetch students with assigned exams
$student_query = mysqli_query($conn, "
    SELECT se.student_id, se.exam_id, e.course_id, c.course_name 
    FROM student_exams se
    JOIN exam_schedule e ON se.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    ORDER BY RAND()");

$students = [];
while ($row = mysqli_fetch_assoc($student_query)) {
    $students[] = $row;
}

// Initialize seat plan
$seat_plan = array_fill(1, $total_seats, null); // Create empty seats

$assigned_seats = [];

foreach ($students as $student) {
    $student_id = $student['student_id'];
    $exam_id = $student['exam_id'];
    $course_id = $student['course_id'];

    // Find an available seat ensuring the course gap
    for ($i = 1; $i <= $total_seats; $i++) {
        if ($seat_plan[$i] === null) {
            // Ensure a gap for the same course
            if (
                ($i > 1 && isset($seat_plan[$i - 1]) && $seat_plan[$i - 1]['course_id'] == $course_id) ||
                ($i < $total_seats && isset($seat_plan[$i + 1]) && $seat_plan[$i + 1]['course_id'] == $course_id)
            ) {
                continue;
            }

            // Assign seat
            $seat_plan[$i] = [
                'student_id' => $student_id,
                'exam_id' => $exam_id,
                'course_id' => $course_id
            ];
            $assigned_seats[$student_id] = $i;


            mysqli_query($conn, "INSERT INTO seat_assignments (student_id, exam_id, room_id, seat_number) 
                                 VALUES ('$student_id', '$exam_id', '$room_id', '$i')");

            break;
        }
    }
}

// Update available seats in the room
mysqli_query($conn, "UPDATE rooms SET available_seats = available_seats - " . count($assigned_seats) . " WHERE room_id = '$room_id'");

echo "<script>alert('Seats assigned successfully!'); window.location.href='view_seating.php';</script>";