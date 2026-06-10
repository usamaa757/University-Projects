<?php
include 'header.php';
include 'db.php';

// Fetch all seating data
$seating_query = mysqli_query($conn, "
    SELECT 
        sa.student_id, 
        s.student_name, 
        sa.room_id, 
        sa.row_number, 
        sa.column_number, 
        r.room_name,
        c.course_name
    FROM seat_assignments sa
    JOIN students s ON sa.student_id = s.student_id
    JOIN rooms r ON sa.room_id = r.room_id
    JOIN courses c ON sa.course_id = c.course_id
    ORDER BY sa.room_id, sa.row_number, sa.column_number ASC
");


// Organize seats by room
$rooms = [];
$max_col = 0;
$max_row = 0;
while ($row = mysqli_fetch_assoc($seating_query)) {
    $room_id = $row['room_id'];
    $row_no = $row['row_number'];
    $col_no = $row['column_number'];

    $max_row = max($max_row, $row_no);
    $max_col = max($max_col, $col_no);

    if (!isset($rooms[$room_id])) {
        $rooms[$room_id] = [
            'room_name' => $row['room_name'],
            'seats' => []
        ];
    }
    $rooms[$room_id]['seats'][$row_no][$col_no] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Seating Chart</title>
    <style>
    table {
        width: 95%;
        margin: auto;
        border-collapse: collapse;
        margin-bottom: 40px;
    }

    td,
    th {
        border: 1px solid #333;
        padding: 10px;
        text-align: center;
        min-width: 120px;
        font-family: 'Segoe UI', sans-serif;
        border-radius: 8px;
        box-shadow: 0 0 5px #ddd;
        transition: transform 0.2s;
    }

    td:hover {
        transform: scale(1.03);
    }

    .empty {
        background-color: #f0f0f0;
        color: #999;
    }

    h3 {
        margin-top: 40px;
    }
    </style>
</head>

<body>

    <h2>Seating Arrangements (Visual Layout)</h2>

    <?php
    $alphabet = range('A', 'Z');

    foreach ($rooms as $room): ?>
    <h3>Room: <?= htmlspecialchars($room['room_name']) ?></h3>
    <table>
        <?php
            $seats = $room['seats'];
            ksort($seats); // Ascending row order

            for ($r = 1; $r <= $max_row; $r++) {
                echo "<tr >";
                for ($c = 1; $c <= $max_col; $c++) {
                    $seat_label = $alphabet[$r - 1] . $c;

                    if (isset($seats[$r][$c])) {
                        $seat = $seats[$r][$c];
                        $color = '#' . substr(md5($seat['course_name']), 0, 6);

                        echo "<td style='background-color: $color; color: #fff;'>
                            <strong>{$seat['student_name']}</strong><br>
                            <small>{$seat['course_name']}</small><br>
                            <small>Seat: $seat_label</small>
                        </td>";
                    } else {
                        echo "<td class='empty'>
                            <em>Empty</em><br>
                            <small>Seat: $seat_label</small>
                        </td>";
                    }
                }
                echo "</tr>";
            }
            ?>
    </table>
    <?php endforeach; ?>

</body>

</html>