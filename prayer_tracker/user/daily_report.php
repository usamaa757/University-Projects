<?php
include 'header.php';
include '../db.php';


$today = date('Y-m-d');
$user_id = $_SESSION['user_id'];
$dailyReport = [];

$stmt = $conn->prepare("
    SELECT n.prayer_name, nr.status, nr.date
    FROM prayer_records nr
    JOIN prayer n ON nr.prayer_id = n.prayer_id
    WHERE nr.user_id = ? AND nr.date = ? ORDER BY n.prayer_id ASC
");
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $dailyReport[] = $row;
}

$stmt->close();


?>
<div class="container mt-5">
    <h4 class="fw-bold mb-3 text-center">🗓️ Daily Prayer Report (<?= date("d M, Y") ?>)</h4>
    <div class="table-responsive">
        <table class="table table-bordered text-center table-hover table-striped ">
            <thead class="table-primary">
                <tr>
                    <th>Prayer</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dailyReport as $prayer): ?>
                <tr>
                    <td><?= htmlspecialchars($prayer['prayer_name']) ?></td>
                    <td>
                        <?php
                            if ($prayer['status']  == 'completed') {
                                echo '<span class="badge bg-success">Completed</span>';
                            } elseif ($prayer['status'] == 'qaza') {
                                echo '<span class="badge bg-danger">Qaza Pending</span>';
                            } else {
                                echo '<span class="badge bg-secondary">Not Recorded</span>';
                            }
                            ?>
                    </td>
                    <td><?= htmlspecialchars($prayer['date']) ?></td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>