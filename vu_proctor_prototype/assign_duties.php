<?php
include 'navbar.php';
include 'db.php';

// Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $exam_id = intval($_POST['exam_id']);
    $user_id = intval($_POST['user_id']);

    // Prevent duplicate (same exam & user)
    $checkDup = $conn->prepare("SELECT id FROM duties WHERE exam_id = ? AND user_id = ?");
    $checkDup->bind_param("ii", $exam_id, $user_id);
    $checkDup->execute();
    $dupResult = $checkDup->get_result();

    if ($dupResult->num_rows > 0) {
        $error = "This person is already assigned to this exam!";
    } else {
        // Prevent scheduling conflicts (same date)
        $check = $conn->prepare("
            SELECT d.id FROM duties d
            JOIN exams e1 ON d.exam_id = e1.id
            JOIN exams e2 ON e2.id = ?
            WHERE d.user_id = ? AND e1.exam_date = e2.exam_date
        ");
        $check->bind_param("ii", $exam_id, $user_id);
        $check->execute();
        $conflict = $check->get_result();

        if ($conflict->num_rows > 0) {
            $error = "This person already has duty on the same date!";
        } else {
            // Get exam info
            $examQ = $conn->prepare("SELECT exam_date, center, session FROM exams WHERE id=?");
            $examQ->bind_param("i", $exam_id);
            $examQ->execute();
            $examData = $examQ->get_result()->fetch_assoc();

            $exam_session = strtolower(trim($examData['session']));
            $exam_center  = strtolower(trim($examData['center']));

            // Get user info
            $userQ = $conn->prepare("SELECT availability, center_preferences FROM users WHERE id=?");
            $userQ->bind_param("i", $user_id);
            $userQ->execute();
            $userData = $userQ->get_result()->fetch_assoc();

            $user_availability = strtolower(trim($userData['availability']));
            $user_preference   = strtolower(trim($userData['center_preferences']));

            // ✅ Availability check (ENUM, direct comparison)
            if ($user_availability !== $exam_session) {
                $error = "This person is only available in the $user_availability, not in the $exam_session!";
            }
            // ✅ Preference check (if any preference is set)
            elseif (!empty($user_preference) && strpos($user_preference, $exam_center) === false) {
                $error = "This person did not select this exam center as a preference!";
            } else {
                // Safe to assign
                $sql = $conn->prepare("INSERT INTO duties (exam_id, user_id) VALUES (?,?)");
                $sql->bind_param("ii", $exam_id, $user_id);
                if ($sql->execute()) {
                    $msg = "Duty assigned successfully!";
                } else {
                    $error = "Error: " . $conn->error;
                }
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $duty_id = intval($_GET['delete']);
    $conn->query("DELETE FROM duties WHERE id = $duty_id");
    $msg = "Duty removed successfully!";
}

// Fetch exams and users for dropdowns
$exams = $conn->query("SELECT id, exam_name, exam_date, center, session FROM exams ORDER BY exam_date ASC");
$users = $conn->query("SELECT id, full_name, role, availability FROM users WHERE role IN ('superintendent','invigilator')");

// Fetch assigned duties
$assigned = $conn->query("
    SELECT d.id as duty_id, e.exam_name, e.exam_date, e.center, u.full_name, u.role, u.availability
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    JOIN users u ON d.user_id = u.id
    ORDER BY e.exam_date ASC
");
?>


<div class="container">
    <h2>Assign Duties</h2>
    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>

    <!-- Assignment Form -->
    <form method="post">
        <label>Select Exam:</label>
        <select name="exam_id" required>
            <?php while ($exam = $exams->fetch_assoc()): ?>
            <option value="<?= $exam['id'] ?>">
                <?= $exam['exam_name'] . " - " . $exam['exam_date'] . " (" . $exam['center'] . " - " . ucfirst($exam['session']) . ")" ?>
            </option>
            <?php endwhile; ?>
        </select>

        <label>Select Person:</label>
        <select name="user_id" required>
            <?php while ($user = $users->fetch_assoc()): ?>
            <option value="<?= $user['id'] ?>">
                <?= $user['full_name'] . " (" . $user['role'] . ")" . " - Availability: " . ucfirst($user['availability']) ?>
            </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="assign">Assign Duty</button>
    </form>

    <!-- Current Assignments -->
    <h3 style="margin-top:30px;">Current Assignments</h3>
    <table>
        <tr>
            <th>Exam</th>
            <th>Date</th>
            <th>Center</th>
            <th>Assigned To</th>
            <th>Availability</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $assigned->fetch_assoc()): ?>
        <tr>
            <td><?= $row['exam_name'] ?></td>
            <td><?= $row['exam_date'] ?></td>
            <td><?= $row['center'] ?></td>
            <td><?= $row['full_name'] ?></td>
            <td><?= ucfirst($row['availability']) ?></td>
            <td><?= ucfirst($row['role']) ?></td>
            <td><a class="delete" href="?delete=<?= $row['duty_id'] ?>"
                    onclick="return confirm('Remove this duty?');">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>

</html>