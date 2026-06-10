<?php
include 'db.php';

// --- PAYMENT RATES ---
$rate_superintendent = 1200;
$rate_invigilator = 800;

// --- AUTO PAYMENT GENERATION ---
$users = $conn->query("
    SELECT DISTINCT u.id, u.full_name, u.role
    FROM users u
    JOIN duties d ON u.id = d.user_id
    WHERE d.verified_by IS NOT NULL
");

while ($user = $users->fetch_assoc()) {
    $user_id = $user['id'];
    $role = $user['role'];

    // ✅ Count total verified duties in current month
    $duties = $conn->query("
        SELECT COUNT(*) AS total
        FROM duties
        WHERE user_id = $user_id
          AND attendance_status = 'present'
          AND verified_by IS NOT NULL
          AND MONTH(verified_at) = MONTH(CURDATE())
          AND YEAR(verified_at) = YEAR(CURDATE())
    ")->fetch_assoc()['total'];

    // ✅ Count total leave *days* overlapping this month
    $leaves = $conn->query("
        SELECT 
            SUM(
                DATEDIFF(
                    LEAST(end_date, LAST_DAY(CURDATE())), 
                    GREATEST(start_date, DATE_FORMAT(CURDATE(), '%Y-%m-01'))
                ) + 1
            ) AS total_days
        FROM leaves
        WHERE user_id = $user_id
          AND status = 'approved'
          AND (
              start_date <= LAST_DAY(CURDATE()) 
              AND end_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
          )
    ")->fetch_assoc()['total_days'];

    $leaves = $leaves ?: 0; // handle NULL

    // ✅ Calculate totals
    $total_present = max(0, $duties - $leaves);
    $rate = ($role === 'superintendent') ? $rate_superintendent : $rate_invigilator;
    $amount = $total_present * $rate;

    // ✅ Prevent duplicate payments for same month
    $check = $conn->query("
        SELECT id FROM payments 
        WHERE user_id = $user_id 
        AND MONTH(last_updated) = MONTH(CURDATE())
        AND YEAR(last_updated) = YEAR(CURDATE())
        LIMIT 1
    ");

    if ($check->num_rows === 0) {
        // Insert if no record for this month
        $conn->query("
            INSERT INTO payments (user_id, total_duties, leaves, total_present, amount, status, last_updated)
            VALUES ($user_id, $duties, $leaves, $total_present, $amount, 'pending', NOW())
        ");
    } else {
        // Update existing month record
        $conn->query("
            UPDATE payments 
            SET total_duties = $duties,
                leaves = $leaves,
                total_present = $total_present,
                amount = $amount,
                last_updated = NOW()
            WHERE user_id = $user_id 
              AND MONTH(last_updated) = MONTH(CURDATE())
              AND YEAR(last_updated) = YEAR(CURDATE())
        ");
    }
}

echo "✅ Automatic payment generation completed successfully at " . date("Y-m-d H:i:s");
