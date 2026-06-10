<?php
include 'db.php';
include 'header.php';

// Check if logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$studentId = $_SESSION['student_id'];

// Fetch student + seating info
$sql = "
    SELECT s.student_name, s.email, c.course_name, sa.row, sa.columns, sa.seat_number
    FROM students s
    LEFT JOIN courses c ON s.course_id = c.course_id
    LEFT JOIN seating_arrangements sa ON sa.student_id = s.student_id
    WHERE s.student_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $studentId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

?>

<div class="container mt-5">
    <h2>🎓 Welcome, <?= htmlspecialchars($data['student_name']) ?></h2>
    <p class="text-muted">Email: <?= htmlspecialchars($data['email']) ?></p>

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            🪑 Your Seating Assignment
        </div>
        <div class="card-body">
            <?php if ($data['row'] && $data['columns']): ?>
            <p><strong>Course:</strong> <?= htmlspecialchars($data['course_name']) ?></p>
            <p><strong>Row:</strong> <?= $data['row'] ?></p>
            <p><strong>Column:</strong> <?= $data['columns'] ?></p>
            <p><strong>Seat Number:</strong> <?= $data['seat_number']; ?></p>
            <?php else: ?>
            <p class="text-danger">Your seating assignment has not been set yet. Please check back later.</p>
            <?php endif; ?>
        </div>
    </div>

</div>