<?php
include 'navbar.php';
require 'db.php';

// Initialize search variables with proper sanitization
$student_id = isset($_GET['student_id']) ? mysqli_real_escape_string($conn, $_GET['student_id']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$author = isset($_GET['author']) ? mysqli_real_escape_string($conn, $_GET['author']) : '';
$program = isset($_GET['program']) ? mysqli_real_escape_string($conn, $_GET['program']) : '';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($conn, $_GET['date_to']) : '';

// Base query
$query = "
    SELECT s.title AS paper_title,
           u.full_name AS student_name,
           u.student_id,
           u.program AS study_program,
           a.title AS assignment_title,
           a.category,
           sv.file_path,
           sv.original_filename,
           s.created_at
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    JOIN assignments a ON s.assignment_id = a.id
    LEFT JOIN submission_versions sv ON sv.id = (
        SELECT id FROM submission_versions
        WHERE submission_id = s.id
        ORDER BY version_no DESC LIMIT 1
    )
    WHERE s.status = 'Accepted and Published'
";

// Fetch unique categories
$categories_result = mysqli_query($conn, "SELECT DISTINCT category FROM assignments");
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row['category'];
}
// Fetch unique programs
$programs_result = mysqli_query($conn, "SELECT DISTINCT program FROM users WHERE role='student'");
$programs = [];
while ($row = mysqli_fetch_assoc($programs_result)) {
    $programs[] = $row['program'];
}

// Build dynamic filters
$conditions = [];
if (!empty($student_id)) $conditions[] = "u.student_id = '$student_id'";
if (!empty($author)) $conditions[] = "(u.role = 'student' AND u.full_name LIKE '%$author%')";
if (!empty($category))   $conditions[] = "a.category = '$category'";
if (!empty($program))    $conditions[] = "u.program = '$program'";
if (!empty($date_from))  $conditions[] = "DATE(s.created_at) >= '$date_from'";
if (!empty($date_to))    $conditions[] = "DATE(s.created_at) <= '$date_to'";

if (count($conditions) > 0) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY s.created_at DESC";

// Debug: Show the final query (remove this after testing)
echo "<!-- DEBUG QUERY: " . htmlspecialchars($query) . " -->";

$published_result = mysqli_query($conn, $query);

// Check for query errors
if (!$published_result) {
    echo "<!-- Query Error: " . mysqli_error($conn) . " -->";
    $published_result = false;
}
?>

<div class="pcontainer">
    <h2>Published Research Papers</h2>

    <!-- Search Form -->
    <form method="GET" class="form">
        <input type="text" name="student_id" placeholder="Student ID" value="<?= htmlspecialchars($student_id) ?>">
        <input type="text" name="author" placeholder="Author Name" value="<?= htmlspecialchars($author) ?>">
        <select name="program">
            <option value="">All Programs</option>
            <?php foreach ($programs as $p): ?>
            <option value="<?= htmlspecialchars($p) ?>" <?= ($p == $program) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <!-- Added this field -->
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= ($c == $category) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
        <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
        <button type="submit" class="btn btn-blue">Search</button>
        <button type="button" class="btn btn-gray" onclick="location.href='?'">Clear Filters</button>
        <!-- Added clear button -->
    </form>

    <?php if ($published_result && mysqli_num_rows($published_result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Paper Title</th>
                <th>Student</th>
                <th>Student ID</th>
                <th>Category</th>
                <th>Assignment</th>
                <th>Uploaded File</th>
                <th>Published On</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($paper = mysqli_fetch_assoc($published_result)): ?>
            <tr>
                <td><?= htmlspecialchars($paper['paper_title']) ?></td>
                <td><?= htmlspecialchars($paper['student_name']) ?></td>
                <td><?= htmlspecialchars($paper['student_id']) ?></td>
                <td><?= htmlspecialchars($paper['category']) ?></td>
                <td><?= htmlspecialchars($paper['assignment_title']) ?></td>
                <td>
                    <?php if (!empty($paper['file_path']) && file_exists($paper['file_path'])): ?>
                    <a href="<?= htmlspecialchars($paper['file_path']) ?>" target="_blank">
                        <?= htmlspecialchars($paper['original_filename']) ?>
                    </a>
                    <?php else: ?>
                    File not available
                    <?php endif; ?>
                </td>
                <td><?= date('d-m-Y', strtotime($paper['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php else: ?>
    <p class="msg">
        <?php
            if (!$published_result) {
                echo "Database error occurred.";
            } else {
                echo "No published research papers found for the selected filters.";
            }
            ?>
    </p>
    <?php endif; ?>
</div>

<style>
.pcontainer {
    width: 95%;
    margin: 40px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

h2 {
    color: #003366;
}

.form {
    margin-bottom: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 5px;
}

form input,
form select,
form button {
    padding: 8px;
    margin-right: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.msg {
    text-align: center;
    color: red;
    font-weight: bold;
    margin-top: 20px;
    padding: 20px;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
}
</style>