<?php
include '../other/db_connection.php';
include 'header.php';

// Fetch distinct classes from the `classes` table
$query = "SELECT class_id, class_name FROM classes ORDER BY class_name ASC";
$result = $conn->query($query);
$classes = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
} else {
    echo "Error fetching classes: " . $conn->error;
}

// Get the selected `class_id` and `search` keyword from the request
$selectedClassID = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$searchKeyword = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Prepare the SQL query to fetch students based on selected `class_id` and search keyword
$sql = "SELECT s.student_id, s.student_name, s.gender, s.dob, s.email, c.class_name
        FROM students s
        JOIN classes c ON s.class_id = c.class_id
        WHERE s.status='pending'";

// Add `class_id` condition to the query if a class is selected
if ($selectedClassID) {
    $sql .= " AND s.class_id = ?";
}

// Add search keyword condition to the query if provided
if (!empty($searchKeyword)) {
    $sql .= " AND s.student_name LIKE ?";
}

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind parameters based on the selected conditions
if ($selectedClassID && !empty($searchKeyword)) {
    $searchTerm = "%$searchKeyword%";
    $stmt->bind_param("is", $selectedClassID, $searchTerm);
} elseif ($selectedClassID) {
    $stmt->bind_param("i", $selectedClassID);
} elseif (!empty($searchKeyword)) {
    $searchTerm = "%$searchKeyword%";
    $stmt->bind_param("s", $searchTerm);
}

// Execute the statement and fetch students
$students = [];
if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
} else {
    echo "Error executing query: " . $stmt->error;
}

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Students</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <header>
        <h1>View Students</h1>
    </header>

    <!-- Form to select class -->
    <form action="" method="GET">
        <label for="class_id">Select Class:</label>
        <select name="class_id" id="class_id" onchange="this.form.submit()">
            <option value="">Choose Class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class['class_id']; ?>" <?php if ($class['class_id'] == $selectedClassID) echo 'selected'; ?>>
                    <?php echo $class['class_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Form to search by name -->
    <form action="" method="GET">
        <label for="search">Search by Name:</label>
        <input type="text" id="search" name="search" placeholder="Enter student name" value="<?php echo htmlspecialchars($searchKeyword); ?>">
        <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($selectedClassID); ?>">
        <input type="submit" value="Search">
    </form>

    <!-- Display student data in a table -->
    <?php if (!empty($students)): ?>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Class</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                        <td><?php echo htmlspecialchars($student['dob']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No students found.</p>
    <?php endif; ?>
</body>
</html>
