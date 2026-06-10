<?php
include 'db.php';
include 'header.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Fetch all seats
$seats = $conn->query("
    SELECT sa.*, s.student_name, s.course_id, c.course_name 
    FROM seating_arrangements sa
    JOIN students s ON sa.student_id = s.student_id
    LEFT JOIN courses c ON s.course_id = c.course_id
");

// Organize seats into grid
$grid = [];
$maxRow = $maxCol = 0;
while ($seat = $seats->fetch_assoc()) {
    $r = $seat['row'];
    $c = $seat['columns'];
    $grid[$r][$c] = $seat;
    if ($r > $maxRow) $maxRow = $r;
    if ($c > $maxCol) $maxCol = $c;
}
?>

<div class="container mt-4">
    <h2>Seating Arrangement View</h2>
    <a href="export_pdf.php" class="btn btn-primary mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Download PDF
    </a>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th></th>
                    <?php for ($c = 1; $c <= $maxCol; $c++): ?>
                    <th>Col <?= $c ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php for ($r = 1; $r <= $maxRow; $r++): ?>
                <tr>
                    <th class="table-dark">Row <?= $r ?></th>
                    <?php for ($c = 1; $c <= $maxCol; $c++): ?>
                    <?php if (isset($grid[$r][$c])):
                                $seat = $grid[$r][$c];
                            ?>
                    <td class="p-2" style="background-color: #e6f7ff;">
                        <strong><?= htmlspecialchars($seat['student_name']) ?></strong><br>
                        <small><?= htmlspecialchars($seat['course_name']) ?></small><br>
                        Seat No: <small><?= htmlspecialchars($seat['seat_number']) ?></small><br>
                        <span class="badge bg-secondary">ID: <?= $seat['student_id'] ?></span>
                    </td>
                    <?php else: ?>
                    <td style="background-color: #f9f9f9;">-</td>
                    <?php endif; ?>
                    <?php endfor; ?>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>