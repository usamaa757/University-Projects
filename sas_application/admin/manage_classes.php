<?php
include 'header.php';
ob_start();
// Include the database connection file
include '../other/db_connection.php';
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $class_name = $_POST['class_name'];

    // Prepare the SQL statement to insert the class
    $stmt = $conn->prepare("INSERT INTO classes (class_name) VALUES (?)");
    $stmt->bind_param("s", $class_name);

    // Execute the query
    if ($stmt->execute()) {
        $message = "Class added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to the same page with a success/failure message
    header("Location: manage_classes.php?message=" . urlencode($message));
    exit;
}

// Fetch all classes from the database
$stmt = $conn->prepare("SELECT class_id, class_name FROM classes");
$stmt->execute();
$result = $stmt->get_result();
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class</title>
    <!-- <link rel="stylesheet" href="../css/form.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #0f582d;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            color: red;
        }
        .class-list {
            margin-top: 20px;
        }
        .class-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .class-list table, .class-list th, .class-list td {
            border: 1px solid #ddd;
        }
        .class-list th, .class-list td {
            padding: 10px;
            text-align: left;
        }
        .class-list th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add a New Class</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="form-group">
                <label for="class_name">Class Name:</label>
                <input type="text" id="class_name" name="class_name" required>
            </div>
            <div class="form-group">
                <button type="submit">Add Class</button>
            </div>
            <?php
            // Display message if available
            if (isset($_GET['message'])) {
                echo "<p class='message'>" . htmlspecialchars($_GET['message']) . "</p>";
            }
            ?>
        </form>

        <!-- Display the list of classes -->
        <div class="class-list">
            <h3>Existing Classes</h3>
            <table>
                <tr>
                    <th>Class ID</th>
                    <th>Class Name</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['class_id'] ?></td>
                        <td><?= $row['class_name'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <?php
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
