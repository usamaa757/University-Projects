<?php
include 'header.php';
include '../db.php';
$user_id = $_SESSION['user_id'];
$username = $_SESSION['name'];
$qaza_prayers = [];

// Qaza prayer counts per prayer
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

// Total counts
$completed = $qaza = 0;
$query = "SELECT status, COUNT(*) as count FROM prayer_records WHERE user_id = ? GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['status'] == 'completed') $completed = $row['count'];
    if ($row['status'] == 'qaza') $qaza = $row['count'];
}
$stmt->close();
$conn->close();
?>

<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Welcome, <?= htmlspecialchars($username) ?> 👋</h2>
        <p class="text-muted">Here is a summary of your prayer tracking</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="bg-white shadow-sm p-4 text-center stat-box border-start border-success border-5">
                <h4 class="text-success">Completed Prayers</h4>
                <h2 class="fw-bold"><?= $completed ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white shadow-sm p-4 text-center stat-box border-start border-danger border-5">
                <h4 class="text-danger">Qaza Prayers</h4>
                <h2 class="fw-bold"><?= $qaza ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <h4 class="text-center mb-3">📊 Qaza Prayers Summary</h4>


    <div class="row g-4 justify-content-center">
        <?php
        $prayers = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
        foreach ($prayers as $prayer):
            $count = $qaza_prayers[$prayer] ?? 0;
        ?>
            <div class="col-md-2 col-lg-2">
                <div class="card text-center stat-box shadow-sm">
                    <div class="card-body">
                        <p class="card-title"><?= htmlspecialchars($prayer) ?></p>
                        <p class="card-text text-danger"><?= $count ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    &copy; <?= date("Y") ?> Prayer Tracker | All rights reserved
</footer>

</body>

</html>