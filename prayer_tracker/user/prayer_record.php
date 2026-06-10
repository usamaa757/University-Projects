<?php
include 'header.php';
include '../db.php';
$user_id = $_SESSION['user_id'];

// Get selected prayer filter if available
$selected_prayer = isset($_GET['prayer_name']) ? $_GET['prayer_name'] : '';

// Fetch unique prayer names for the filter dropdown
$prayer_query = "SELECT DISTINCT prayer_name FROM prayer";
$prayer_result = $conn->query($prayer_query);

// Prepare the main query with optional filter
$query = "SELECT prayer.prayer_name, prayer_records.status, prayer_records.date
          FROM prayer_records
          JOIN prayer ON prayer.prayer_id = prayer_records.prayer_id
          WHERE prayer_records.user_id = ?";

if (!empty($selected_prayer)) {
    $query .= " AND prayer.prayer_name = ?";
}

$query .= " ORDER BY prayer_records.date DESC, prayer.prayer_id ASC";
$stmt = $conn->prepare($query);

if (!empty($selected_prayer)) {
    $stmt->bind_param("is", $user_id, $selected_prayer);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$prayer_records = [];
while ($row = $result->fetch_assoc()) {
    $prayer_records[] = $row;
}
?>

<div class="container mt-5">
    <h4 class="fw-bold mb-3 text-center">📅 All Prayer Records</h4>

    <!-- Filter Dropdown -->
    <form method="GET" class="mb-4 text-center">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <select name="prayer_name" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Filter by Prayer --</option>
                    <?php while ($row = $prayer_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['prayer_name']) ?>"
                        <?= $row['prayer_name'] == $selected_prayer ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['prayer_name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </form>

    <!-- Prayer Records Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped  text-center">
            <thead class="table-primary">
                <tr>
                    <th>Prayer Name</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($prayer_records)): ?>
                <tr>
                    <td colspan="3" class="text-center">No prayer records found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($prayer_records as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['prayer_name']) ?></td>
                    <td>
                        <?php
                                if ($record['status']  == 'completed') {
                                    echo '<span class="badge bg-success">Completed</span>';
                                } elseif ($record['status'] == 'qaza') {
                                    echo '<span class="badge bg-danger">Qaza Pending</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">Not Recorded</span>';
                                }
                                ?>
                    </td>
                    <td><?= date("d M, Y", strtotime($record['date'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>