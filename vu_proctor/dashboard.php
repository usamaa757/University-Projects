<?php
// admin_dashboard.php
// Place this file in your project (requires db.php, navbar.php)
// Assumes:
//  - sessions started in navbar.php or db.php (if not, add session_start())
//  - tables: users, exams, duties, leaves, payments
//  - duties.attendance_status in ('present','absent','unmarked')
//  - duties.verified_by is NULL or admin user id
//  - leaves.status includes 'approved'
//  - payments.status includes 'pending','processed'

include 'navbar.php';
include 'db.php';

// Access control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

// Utility: safe fetch single value
function fetch_one($conn, $sql)
{
    $r = $conn->query($sql);
    if (!$r) return 0;
    $row = $r->fetch_array();
    return $row[0] ?? 0;
}

// --- KPIs ---
$total_exams = fetch_one($conn, "SELECT COUNT(*) FROM exams");
$total_duties = fetch_one($conn, "SELECT COUNT(*) FROM duties");
$total_users = fetch_one($conn, "SELECT COUNT(*) FROM users WHERE role IN ('superintendent','invigilator')");

$present_count = fetch_one($conn, "SELECT COUNT(*) FROM duties WHERE attendance_status = 'present' AND verified_by IS NOT NULL");
$verified_total = fetch_one($conn, "SELECT COUNT(*) FROM duties WHERE verified_by IS NOT NULL");
$attendance_rate = ($verified_total > 0) ? round(($present_count / $verified_total) * 100, 2) : 0;

$pending_reports = fetch_one($conn, "SELECT COUNT(*) FROM duties WHERE report_file IS NULL AND attendance_status='present' AND verified_by IS NOT NULL");
$pending_payments = fetch_one($conn, "SELECT COUNT(*) FROM payments WHERE status = 'pending'");

// --- Duty allocation by center (for bar) ---
$centers_q = $conn->query("
    SELECT center, COUNT(*) AS cnt
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    GROUP BY center
    ORDER BY cnt DESC
");
$centers = [];
$centers_counts = [];
while ($r = $centers_q->fetch_assoc()) {
    $centers[] = $r['center'];
    $centers_counts[] = (int)$r['cnt'];
}

// --- Attendance by role (doughnut) ---
$att_role_q = $conn->query("
    SELECT u.role,
           SUM(CASE WHEN d.attendance_status='present' AND d.verified_by IS NOT NULL THEN 1 ELSE 0 END) AS present_count,
           SUM(CASE WHEN d.verified_by IS NOT NULL THEN 1 ELSE 0 END) AS verified_count
    FROM users u
    LEFT JOIN duties d ON u.id = d.user_id
    WHERE u.role IN ('superintendent','invigilator')
    GROUP BY u.role
");
$roles = [];
$role_present = [];
while ($r = $att_role_q->fetch_assoc()) {
    $roles[] = ucfirst($r['role']);
    $role_present[] = (int)$r['present_count'];
}

// --- Monthly trend for last 6 months (line) ---
$trend_months = [];
$trend_counts = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-$i month"));
    $label = date('M Y', strtotime("-$i month"));
    $trend_months[] = $label;
    $count = fetch_one($conn, "
        SELECT COUNT(*) FROM duties
        WHERE attendance_status='present' AND verified_by IS NOT NULL
        AND DATE_FORMAT(verified_at, '%Y-%m') = '$m'
    ");
    $trend_counts[] = (int)$count;
}

// --- Top performers (by verified present duties in last 6 months) ---
$top_q = $conn->query("
    SELECT u.id, u.full_name, u.role, COUNT(*) AS present_count
    FROM users u
    JOIN duties d ON u.id = d.user_id
    WHERE d.attendance_status='present' AND d.verified_by IS NOT NULL
      AND d.verified_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY u.id
    ORDER BY present_count DESC
    LIMIT 10
");

// --- Recent duties (latest 10) ---
$recent_q = $conn->query("
    SELECT d.id, e.exam_name, e.exam_date, e.center, u.full_name, d.attendance_status, d.verified_by, d.verified_at
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    JOIN users u ON d.user_id = u.id
    ORDER BY e.exam_date DESC, d.id DESC
    LIMIT 10
");

// --- Recent reports (latest 8) ---
$reports_q = $conn->query("
    SELECT d.id, e.exam_name, e.exam_date, e.center, u.full_name, d.report_file, d.report_uploaded_at
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    JOIN users u ON d.user_id = u.id
    WHERE d.report_file IS NOT NULL
    ORDER BY d.report_uploaded_at DESC
    LIMIT 8
");

// Prepare JSON data for charts
$chart_centers_labels = json_encode($centers);
$chart_centers_data   = json_encode($centers_counts);

$chart_roles_labels = json_encode($roles);
$chart_roles_data   = json_encode($role_present);

$chart_trend_labels = json_encode($trend_months);
$chart_trend_data   = json_encode($trend_counts);

// HTML + inline CSS + Chart.js
?>
<div class="header">
    <h1>Admin Dashboard — Reporting & Analytics</h1>
    <div class="controls">
        <div class="badge">Attendance Rate: <?= $attendance_rate ?>%</div>
        <div style="font-size:13px;color:#6b7280">As of <?= date('F j, Y') ?></div>
    </div>
</div>

<div class="grid">
    <!-- KPI CARDS -->
    <div class="kpi card-1">
        <h3>Total Exams</h3>
        <div class="value"><?= number_format($total_exams) ?></div>
        <div style="font-size:12px;color:#6b7280;margin-top:8px">All exam records</div>
    </div>

    <div class="kpi card-2">
        <h3>Total Duties</h3>
        <div class="value"><?= number_format($total_duties) ?></div>
        <div style="font-size:12px;color:#6b7280;margin-top:8px">All assigned duties</div>
    </div>

    <div class="kpi card-3">
        <h3>Pending Reports</h3>
        <div class="value"><?= number_format($pending_reports) ?></div>
        <div style="font-size:12px;color:#6b7280;margin-top:8px">Reports not uploaded yet</div>
    </div>

    <div class="kpi card-4">
        <h3>Pending Payments</h3>
        <div class="value"><?= number_format($pending_payments) ?></div>
        <div style="font-size:12px;color:#6b7280;margin-top:8px">Payments awaiting processing</div>
    </div>

    <!-- Charts & side -->
    <div class="charts chart-card" style="grid-column: span 8;">
        <h3 style="margin:0 0 12px 0">Duty Allocation by Center</h3>
        <canvas id="centersBar"></canvas>

        <div style="display:flex; gap:12px; margin-top:18px;">
            <div
                style="flex:1; background:#fff; padding:12px; border-radius:8px; box-shadow:0 4px 12px rgba(15,23,42,0.04)">
                <h4 style="margin:0 0 6px 0">Monthly Verified Duties</h4>
                <canvas id="trendLine" style="height:140px"></canvas>
            </div>

            <div
                style="width:220px; background:#fff; padding:12px; border-radius:8px; box-shadow:0 4px 12px rgba(15,23,42,0.04)">
                <h4 style="margin:0 0 6px 0">Attendance by Role</h4>
                <canvas id="roleDonut" style="height:140px"></canvas>
            </div>
        </div>
    </div>

    <div class="sidepan">
        <div class="table-card">
            <h4 style="margin-top:0">Top Performers (6 months)</h4>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Verified Present</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $top_q->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['full_name']) ?></td>
                            <td><?= ucfirst($r['role']) ?></td>
                            <td><?= (int)$r['present_count'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <h4 style="margin-top:0">Recent Reports</h4>
            <table>
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th>Center</th>
                        <th>Superintendent</th>
                        <th>Uploaded</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rr = $reports_q->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($rr['exam_name']) ?> (<?= $rr['exam_date'] ?>)</td>
                            <td><?= htmlspecialchars($rr['center']) ?></td>
                            <td><?= htmlspecialchars($rr['full_name']) ?></td>
                            <td><?= htmlspecialchars($rr['report_uploaded_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Duties table spanning full width -->
    <div class="table-card" style="grid-column: span 12;">
        <h4 style="margin-top:0">Recent Duties</h4>
        <table>
            <thead>
                <tr>
                    <th>Exam</th>
                    <th>Date</th>
                    <th>Center</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Verified At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = $recent_q->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['exam_name']) ?></td>
                        <td><?= htmlspecialchars($d['exam_date']) ?></td>
                        <td><?= htmlspecialchars($d['center']) ?></td>
                        <td><?= htmlspecialchars($d['full_name']) ?></td>
                        <td><?= ucfirst($d['attendance_status']) ?></td>
                        <td><?= $d['verified_at'] ?: '-' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Scripts for charts -->
<script>
    const centersLabels = <?= $chart_centers_labels ?>;
    const centersData = <?= $chart_centers_data ?>;

    const rolesLabels = <?= $chart_roles_labels ?>;
    const rolesData = <?= $chart_roles_data ?>;

    const trendLabels = <?= $chart_trend_labels ?>;
    const trendData = <?= $chart_trend_data ?>;

    // Bar chart - Centers
    new Chart(document.getElementById('centersBar'), {
        type: 'bar',
        data: {
            labels: centersLabels,
            datasets: [{
                label: 'Duties',
                data: centersData,
                borderRadius: 6,
                borderWidth: 0.5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Line chart - trend
    new Chart(document.getElementById('trendLine'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Verified Present',
                data: trendData,
                fill: true,
                tension: 0.3,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Doughnut - roles
    new Chart(document.getElementById('roleDonut'), {
        type: 'doughnut',
        data: {
            labels: rolesLabels,
            datasets: [{
                label: 'Present',
                data: rolesData,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

</body>

</html>