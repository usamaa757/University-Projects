<?php
include 'header.php';
include '../db_connection.php';

// Fetch all reported issues from the database
$query = "SELECT ir.report_id, ir.issue_type, ir.description, ir.uploaded_file, ir.report_date, u.user_name 
          FROM issue_reports ir 
          JOIN users u ON ir.user_id = u.user_id 
          ORDER BY ir.report_date DESC";
$result = $conn->query($query);
?>

<div class="container mt-5 border round shadow p-0">
    <div class=" text-center bg-dark text-white">
        <h3 class="mb-4 p-2">Reported Issues</h3>
    </div>
    <div class="p-3">
        <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Issue Type</th>
                    <th>Description</th>
                    <th>Reported By</th>
                    <th>Report Date</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['report_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['issue_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td><?php echo date("d M, Y H:i", strtotime($row['report_date'])); ?></td>
                    <td>
                        <?php if ($row['uploaded_file']): ?>
                        <a href="<?php echo $row['uploaded_file']; ?>" target="_blank">View File</a>
                        <?php else: ?>
                        No File
                        <?php endif; ?>
                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-info">No issues have been reported yet.</div>
        <?php endif; ?>
    </div>
</div>

</body>

</html>