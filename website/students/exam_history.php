<?php
// Include necessary files and establish database connection
include 'header.php';
include '../db_connection.php';

// Start session to retrieve student_id
$student_id = $_SESSION['student_id'];

// SQL query to fetch multiple exam records, grouped by subject and exam type
$sql = "
    SELECT 
        se.exam_id, 
        se.exam_type, 
        se.subject_id,
        s.subject_name,
        se.marks,
        se.student_exam_id,
        se.result_date
    FROM 
        student_exam se
    LEFT JOIN 
        subjects s ON se.subject_id = s.subject_id
    WHERE 
        se.student_id = ?
    ORDER BY 
        se.result_date DESC"; // Order by result date to show recent exams first

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare an array to store data fetched from the database
$exam_history = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $exam_history[] = $row;
    }
} else {
    $no_data = true;
}

// Close connection
$stmt->close();
$conn->close();
?>

<br><br><br><br><br>
<div class="container ">
    <?php if (isset($no_data) && $no_data): ?>
        <h2>No Exam History Found</h2>
    <?php else: ?>
        <br><br><br><br><br>
        <div class="container border rounded shadow p-0">
            <h3 class="text-white bg-primary p-2">Exam History</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Exam ID</th>
                        <th>Exam Type</th>
                        <th>Marks</th>
                        <th>Subject</th>
                        <th>Result Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exam_history as $exam): ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['exam_id']) ?></td>
                            <td><?= htmlspecialchars($exam['exam_type']) ?>-Term</td>
                            <td><?= htmlspecialchars($exam['marks']) ?></td>
                            <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                            <td><?= htmlspecialchars(date('d M, Y', strtotime($exam['result_date']))) ?></td>
                            <td>
                                <a href="view_exam_details.php?student_exam_id=<?= htmlspecialchars($exam['student_exam_id']) ?>&subject_id=<?= htmlspecialchars($exam['subject_id']) ?>&exam_type=<?= htmlspecialchars($exam['exam_type']) ?>"
                                    class="btn btn-primary">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>

</html>