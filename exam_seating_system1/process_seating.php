<?php
include 'db.php';

// Fetch all students with assigned courses
$student_query = mysqli_query($conn, "
    SELECT sc.student_id, s.student_name, sc.course_id, c.course_name 
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.student_id
    JOIN courses c ON sc.course_id = c.course_id
    ORDER BY sc.course_id ASC");

$students = [];
while ($row = mysqli_fetch_assoc($student_query)) {
    $students[] = $row;
}

// Group students by course
$course_groups = [];
foreach ($students as $student) {
    $course_id = $student['course_id'];
    $course_groups[$course_id][] = $student;
}

// Fetch all available rooms
$room_query = mysqli_query($conn, "SELECT * FROM rooms WHERE available_seats > 0");
$rooms = [];
while ($row = mysqli_fetch_assoc($room_query)) {
    $rooms[] = $row;
}

// Helper to calculate seating layout
function calculateRowsColumns($totalSeats)
{
    $preferred_rows = [3, 4, 5];
    $best_fit = null;
    $min_unused = PHP_INT_MAX;

    foreach ($preferred_rows as $rows) {
        $cols = ceil($totalSeats / $rows);
        $used_seats = $rows * $cols;
        $unused_seats = $used_seats - $totalSeats;

        if ($unused_seats < $min_unused) {
            $min_unused = $unused_seats;
            $best_fit = [$rows, $cols];
        }
    }

    return $best_fit;
}


$seating_assignments = [];
$errors = [];
foreach ($rooms as $room) {
    $room_id = $room['room_id'];
    $room_name = $room['room_name'];
    $capacity = $room['available_seats'];

    [$rows, $cols] = calculateRowsColumns($capacity);
    $seats = array_fill(0, $rows, array_fill(0, $cols, null));

    // Assign students to room without seating near same course
    foreach ($course_groups as $course_id => &$course_students) {
        foreach ($course_students as $index => $student) {
            $assigned = false;

            // Check if student is already assigned a seat in any room for the current course
            $check_query = mysqli_query($conn, "
                SELECT * FROM seat_assignments 
                WHERE student_id = '{$student['student_id']}' 
                AND course_id = '{$course_id}'");

            if (mysqli_num_rows($check_query) > 0) {

                // Add error if already assigned
                $errors[] = "Student '{$student['student_name']}' is already assigned a seat for Course '{$student['course_name']}'.";

                continue; // Skip this student if already assigned
            }

            for ($r = 0; $r < $rows && !$assigned; $r++) {
                for ($c = 0; $c < $cols && !$assigned; $c++) {
                    if ($seats[$r][$c] === null) {
                        $conflict = false;
                        $neighbors = [
                            [$r - 1, $c],
                            [$r + 1, $c],
                            [$r, $c - 1],
                            [$r, $c + 1]
                        ];
                        foreach ($neighbors as [$nr, $nc]) {
                            if (isset($seats[$nr][$nc]) && $seats[$nr][$nc]['course_id'] == $course_id) {
                                $conflict = true;
                                break;
                            }
                        }

                        if (!$conflict) {
                            $seats[$r][$c] = [
                                'student_id' => $student['student_id'],
                                'student_name' => $student['student_name'],
                                'course_id' => $course_id,
                                'course_name' => $student['course_name'],
                                'row' => $r + 1,
                                'col' => $c + 1,
                                'room_id' => $room_id,
                                'room_name' => $room_name
                            ];
                            unset($course_students[$index]);
                            $assigned = true;
                        }
                    }
                }
            }
        }
    }

    foreach ($seats as $row) {
        foreach ($row as $seat) {
            if ($seat !== null) {
                $seating_assignments[] = $seat;

                // Insert seat assignment into the database
                mysqli_query($conn, "
                    INSERT INTO seat_assignments 
                    (student_id, course_id, room_id, `row_number`, `column_number`) 
                    VALUES (
                        '{$seat['student_id']}', 
                        '{$seat['course_id']}', 
                        '{$seat['room_id']}', 
                        '{$seat['row']}', 
                        '{$seat['col']}')");

                // Update the room's available seats
                mysqli_query($conn, "
                    UPDATE rooms 
                    SET available_seats = available_seats - 1 
                    WHERE room_id = '{$seat['room_id']}'");
            }
        }
    }
}
if (!empty($errors)) {
    // Join the errors and display them
    $errorMessages = implode("\n", $errors);
    echo "<script>
        alert('Some students already have assigned seats:\n$errorMessages');
        window.location.href = 'view_seating.php';
    </script>";
} else {
    echo "<script>
        alert('Seats assigned successfully!');
        window.location.href = 'view_seating.php';
    </script>";
}