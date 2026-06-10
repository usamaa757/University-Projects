<?php
include 'navbar.php';
include 'db.php';


$user_id = $_SESSION['user_id'];

// Fetch all duties for this user
$sql = $conn->prepare("
    SELECT e.exam_name, e.exam_date, e.center, u.role 
    FROM duties d
    JOIN users u ON d.user_id = u.id
    JOIN exams e ON d.exam_id = e.id
    WHERE d.user_id = ?
    ORDER BY e.exam_date ASC
");
$sql->bind_param("i", $user_id);
$sql->execute();
$result = $sql->get_result();
?>
<div class="container">
    <h2>📋 My Duty Assignments</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Exam Name</th>
            <th>Date</th>
            <th>Center</th>
            <th>Assigned Role</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['exam_name'] ?></td>
            <td><?= $row['exam_date'] ?></td>
            <td><?= $row['center'] ?></td>
            <td><?= ucfirst($row['role']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <p style="text-align:center;">No duties assigned yet.</p>
    <?php endif; ?>
</div>
</body>

</html>