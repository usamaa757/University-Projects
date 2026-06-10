<?php

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

include 'db.php';
session_start();

// Use student ID from session or from GET
$student_id = $_SESSION['student_id'] ?? ($_GET['student_id'] ?? null);

if ($student_id) {
    // Student-specific query (no exam data)
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
        WHERE sa.student_id = '$student_id'
        ORDER BY sa.room_id, sa.row_number, sa.column_number ASC
    ");
} else {
    // Admin/all-students view
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
}

// Organize seat data
$rooms = [];
$max_col = 0;
$max_row = 0;
$alphabet = range('A', 'Z');

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

// Start HTML output
ob_start();
?>

<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

td,
th {
    border: 1px solid #333;
    padding: 8px;
    text-align: center;
    font-size: 10pt;
}

.empty {
    background-color: #f0f0f0;
    color: #666;
}

h3 {
    margin-top: 30px;
    font-size: 14pt;
}
</style>

<h2>Seating Arrangement <?= $student_id ? 'for Student' : 'Report' ?></h2>

<?php foreach ($rooms as $room): ?>
<h3>Room: <?= htmlspecialchars($room['room_name']) ?></h3>
<table>
    <?php
        $seats = $room['seats'];
        ksort($seats);
        for ($r = 1; $r <= $max_row; $r++) {
            echo "<tr>";
            for ($c = 1; $c <= $max_col; $c++) {
                $seat_label = $alphabet[$r - 1] . $c;
                if (isset($seats[$r][$c])) {
                    $seat = $seats[$r][$c];
                    echo "<td>
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

<?php
$html = ob_get_clean();

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($student_id ? "student_seating.pdf" : "seating_chart.pdf", ["Attachment" => false]);
?>