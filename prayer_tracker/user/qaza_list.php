<?php
include 'header.php';
include '../db.php';
$user_id = $_SESSION['user_id'];
$qaza_prayers = [];

// 1. Fetch count of qaza prayers
$sql = "SELECT prayer.prayer_name, COUNT(*) AS qaza_count 
        FROM prayer_records
        JOIN prayer ON prayer.prayer_id = prayer_records.prayer_id
        WHERE prayer_records.user_id = ? AND prayer_records.status = 'qaza'
        GROUP BY prayer.prayer_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $qaza_prayers[$row['prayer_name']] = $row['qaza_count'];
}
$stmt->close();

// 2. Fetch prayer names for dropdown
$prayer_result = $conn->query("SELECT DISTINCT prayer_name FROM prayer");

// 3. Handle prayer filter
$selected_prayer = $_GET['prayer_name'] ?? '';

$qazaPrayerList = [];
$sql = "SELECT prayer_records.prayer_id, prayer.prayer_name, prayer_records.date
        FROM prayer_records
        JOIN prayer ON prayer.prayer_id = prayer_records.prayer_id
        WHERE prayer_records.user_id = ? AND prayer_records.status = 'qaza'";
if (!empty($selected_prayer)) {
    $sql .= " AND prayer.prayer_name = ?";
}
$sql .= " ORDER BY prayer_records.date DESC";
$stmt = $conn->prepare($sql);
if (!empty($selected_prayer)) {
    $stmt->bind_param("is", $user_id, $selected_prayer);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $qazaPrayerList[] = $row;
}
$stmt->close();

// 4. Handle marking prayer as completed
if (isset($_POST['mark_complete'])) {
    $date = $_POST['date'];
    $prayer_id = $_POST['prayer_id'];

    $update = "UPDATE prayer_records 
               SET status = 'completed' 
               WHERE user_id = ? AND prayer_id = ? AND date = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("iis", $user_id, $prayer_id, $date);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF'] . "?prayer_name=" . urlencode($selected_prayer));
    exit();
}
?>

<div class="container mt-5">
    <h4 class="text-center mb-3">📊 Qaza Prayers Summary</h4>

    <div class="row g-4 justify-content-center">
        <?php
        $prayers = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
        foreach ($prayers as $prayer):
            $count = $qaza_prayers[$prayer] ?? 0;
        ?>
        <div class="col-md-2 col-lg-2">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <p class="card-title"><?= htmlspecialchars($prayer) ?></p>
                    <p class="card-text text-danger"><?= $count ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="container mt-5">
    <h4 class="fw-bold mb-3 text-center">🕌 List of Qaza Prayers</h4>

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

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover text-center">
            <thead class="table-primary">
                <tr>
                    <th>Prayer Name</th>
                    <th>Qaza Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($qazaPrayerList)): ?>
                <tr>
                    <td colspan="3" class="text-center">No qaza prayers recorded.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($qazaPrayerList as $qaza): ?>
                <tr>
                    <td><?= htmlspecialchars($qaza['prayer_name']) ?></td>
                    <td><?= date("d M, Y", strtotime($qaza['date'])) ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="date" value="<?= htmlspecialchars($qaza['date']) ?>">
                            <input type="hidden" name="prayer_id" value="<?= $qaza['prayer_id'] ?>">
                            <button type="submit" name="mark_complete" class="btn btn-sm btn-success">
                                Mark as Complete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>