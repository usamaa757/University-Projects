<?php
include '../other/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_name = $_POST['parent_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $student_id = $_POST['student_id'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO parents (parent_name, email, password, student_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $parent_name, $email, $password, $student_id);

    if ($stmt->execute()) {
        echo "New parent registered successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch students for the dropdown
$students = [];
$result = $conn->query("SELECT student_id, student_name FROM students");

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Parent</title>
</head>
<body>
    <h1>Register Parent</h1>
    <form action="add_parents.php" method="post">
        <div>
            <label for="parent_name">Parent Name:</label>
            <input type="text" id="parent_name" name="parent_name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="student_id">Student:</label>
            <select id="student_id" name="student_id" required>
                <option value="">Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo htmlspecialchars($student['student_id']); ?>">
                        <?php echo htmlspecialchars($student['student_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit">Register Parent</button>
        </div>
    </form>
</body>
</html>
