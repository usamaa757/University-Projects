<?php
include 'db.php';
include 'header.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Configuration

$MIN_DISTANCE_BETWEEN_SAME_COURSE = 2; // seats

$TEACHER_TABLE_ROW = 0; // Front row
$ROW_SPACING_ENABLED = true; // Add empty rows between student rows

if (isset($_POST['generate'])) {
    $ROOM_NAME = $_POST['room_name'];
    $CLASSROOM_CAPACITY = $_POST['capacity'];
    $conn->query("TRUNCATE TABLE seating_arrangements");

    $students = $conn->query("
        SELECT s.student_id, s.student_name, s.course_id, c.course_name 
        FROM students s
        JOIN courses c ON s.course_id = c.course_id
        WHERE s.course_id IS NOT NULL
    ");

    $student_list = [];
    while ($row = $students->fetch_assoc()) {
        $student_list[] = $row;
    }

    $total_students = count($student_list);
    if ($total_students === 0) {
        echo "<div class='alert alert-warning mt-3'>No students with assigned courses.</div>";
        return;
    }

    if ($total_students > $CLASSROOM_CAPACITY) {
        echo "<div class='alert alert-danger mt-3'>Classroom capacity exceeded ($CLASSROOM_CAPACITY). $total_students students found.</div>";
        return;
    }

    $cols = ceil(sqrt($total_students));
    $rows = ceil($total_students / $cols);

    if ($rows > $cols) {
        $temp = $rows;
        $rows = $cols;
        $cols = $temp;
    }

    $visual_rows = $ROW_SPACING_ENABLED ? ($rows * 2 - 1) : $rows;
    $grid = array_fill(0, $visual_rows, array_fill(0, $cols, null));
    $seat_numbers = array_fill(0, $visual_rows, array_fill(0, $cols, 0));
    $assigned_students = 0;

    $hasConflict = function ($grid, $row, $col, $course_id) use ($MIN_DISTANCE_BETWEEN_SAME_COURSE) {
        for (
            $r = max(0, $row - $MIN_DISTANCE_BETWEEN_SAME_COURSE);
            $r <= min(count($grid) - 1, $row + $MIN_DISTANCE_BETWEEN_SAME_COURSE);
            $r++
        ) {
            for (
                $c = max(0, $col - $MIN_DISTANCE_BETWEEN_SAME_COURSE);
                $c <= min(count($grid[0]) - 1, $col + $MIN_DISTANCE_BETWEEN_SAME_COURSE);
                $c++
            ) {
                if ($grid[$r][$c] && $grid[$r][$c]['course_id'] === $course_id) {
                    return true;
                }
            }
        }
        return false;
    };

    shuffle($student_list);

    $unplaced_students = [];
    foreach ($student_list as $student) {
        $placed = false;
        for ($c = 0; $c < $cols; $c++) {
            for ($r = 0; $r < $rows; $r++) {
                $actual_row = $ROW_SPACING_ENABLED ? $r * 2 : $r;
                if (!$grid[$actual_row][$c] && !$hasConflict($grid, $actual_row, $c, $student['course_id'])) {
                    $grid[$actual_row][$c] = $student;
                    $seat_numbers[$actual_row][$c] = ++$assigned_students;
                    $placed = true;
                    break 2;
                }
            }
        }
        if (!$placed) {
            $unplaced_students[] = $student;
        }
    }

    foreach ($unplaced_students as $student) {
        $best_spot = null;
        $min_conflicts = PHP_INT_MAX;

        for ($c = 0; $c < $cols; $c++) {
            for ($r = 0; $r < $rows; $r++) {
                $actual_row = $ROW_SPACING_ENABLED ? $r * 2 : $r;
                if (!$grid[$actual_row][$c]) {
                    $conflicts = countAdjacentConflicts($grid, $actual_row, $c, $student['course_id']);
                    if ($conflicts < $min_conflicts) {
                        $min_conflicts = $conflicts;
                        $best_spot = [$actual_row, $c];
                        if ($min_conflicts == 0) break 2;
                    }
                }
            }
        }

        if ($best_spot) {
            list($r, $c) = $best_spot;
            $grid[$r][$c] = $student;
            $seat_numbers[$r][$c] = ++$assigned_students;
        }
    }

    for ($r = 0; $r < $visual_rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($grid[$r][$c]) {
                $student = $grid[$r][$c];
                $seat_num = $seat_numbers[$r][$c];
                $row_num = $r + 1;
                $col_num = $c + 1;

                $stmt = $conn->prepare("INSERT INTO seating_arrangements 
                    (student_id, row, columns, seat_number, room, course_id) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param(
                    "iiiisi",
                    $student['student_id'],
                    $row_num,
                    $col_num,
                    $seat_num,
                    $ROOM_NAME,
                    $student['course_id']
                );
                $stmt->execute();
            }
        }
    }

    echo "<div class='alert alert-success mt-3'>Seating arrangement generated for $total_students students!</div>";
    echo "<div class='mt-4'>";
    echo "<h4>Seating Arrangement ($visual_rows rows × $cols columns)</h4>";
    echo "<div class='seating-chart'>";

    for ($r = 0; $r < $visual_rows; $r++) {
        echo "<div class='seat-row'>";
        for ($c = 0; $c < $cols; $c++) {
            $student = $grid[$r][$c];
            if ($student) {
                $course_color = getColorForCourse($student['course_id']);
                echo "<div class='seat occupied' style='background-color: $course_color' title='{$student['course_name']}'>";
                echo "<div class='student-name'>{$student['student_name']}</div>";
                echo "<div class='seat-number'>Seat {$seat_numbers[$r][$c]}</div>";
                echo "<div class='course-name'>{$student['course_name']}</div>";
                echo "</div>";
            } else {
                echo "<div class='seat empty'></div>";
            }
        }
        echo "</div>";
    }

    echo "</div>";
    echo "<p class='mt-3'><strong>Teacher's table is at the front (Row 1)</strong></p>";
    echo "<p class='text-muted'><em>Each box is a physical seat. Rows are spaced to prevent line-of-sight copying.</em></p>";
    echo "</div>";
}

function countAdjacentConflicts($grid, $row, $col, $course_id)
{
    $conflicts = 0;
    $directions = [
        [-1, 0],
        [1, 0],
        [0, -1],
        [0, 1],
        [-1, -1],
        [-1, 1],
        [1, -1],
        [1, 1]
    ];
    foreach ($directions as $dir) {
        $r = $row + $dir[0];
        $c = $col + $dir[1];
        if (isset($grid[$r][$c]) && $grid[$r][$c]['course_id'] === $course_id) {
            $conflicts++;
        }
    }
    return $conflicts;
}

function getColorForCourse($course_id)
{
    $colors = [
        '#FFDDC1',
        '#C1FFD7',
        '#C1D8FF',
        '#FFC1F2',
        '#E8FFC1',
        '#FFC1C1',
        '#C1FFF5',
        '#D5C1FF',
        '#FFF8C1',
        '#C1FFFD',
        '#FFC1E3',
        '#C1FFAA'
    ];
    return $colors[$course_id % count($colors)];
}
?>

<div class="container mt-4">
    <h2>Generate Seating for All Students</h2>
    <form method="post" class="row g-3 mt-3">
        <div class="mb-3">
            <label for="room_name">Room</label>
            <input type="text" name="room_name" class="form-control" placeholder="Room" required>
        </div>
        <div class="mb-3">
            <label for="capacity">Room Capacity</label>
            <input type="text" name="capacity" class="form-control" placeholder="Total Seats" required>
        </div>
        <div class="col-md-12">
            <button type="submit" name="generate" class="btn btn-primary w-100">Generate Seating</button>
        </div>
    </form>
</div>

<style>
.seating-chart {
    margin: 20px 0;
    border: 1px solid #ddd;
    padding: 10px;
    background: #f9f9f9;
}

.seat-row {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
    /* extra spacing to simulate visual distance */
}

.seat {
    width: 120px;
    height: 80px;
    margin: 0 5px;
    border: 1px solid #ccc;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.seat.occupied {
    color: #333;
}

.seat.empty {
    background: #f5f5f5;
    opacity: 0.6;
}

.student-name {
    font-weight: bold;
    margin-bottom: 3px;
}

.course-name {
    font-size: 10px;
    color: #444;
    margin-top: 3px;
    text-align: center;
}

.seat-number {
    font-size: 10px;
    color: #666;
}
</style>