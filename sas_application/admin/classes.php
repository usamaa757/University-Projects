<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <link rel="stylesheet" href="../css/form.css">
    <style>
        form {
    background-color: #fff;
    padding: 8px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    
}
    </style>
</head>
<body>
    <header>
    <h2>Create Class</h2>
    </header>
    <!-- Form to create a new class -->
    <form method="POST" action="">
        <label for="name">Class Name:</label>
        <input type="text" id="name" name="name" required>
        <button type="submit" name="action" value="create">Create Class</button>
    </form>
    <header>
    <h2>Manage Classes</h2>
    </header>
    <table border="1">
        <tr>
            <th>Class ID</th>
            <th>Class Name</th>
            <th>Actions</th>
        </tr>
        
        <!-- PHP code to list all classes -->
        <?php
        include '../other/db_connection.php'; // Database connection file
        session_start();
        
        // Check if the user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
            header("Location: ../other/login.php");
            exit();
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            // Handle the different actions (create, edit, delete)
            if ($action === 'create') {
                // Handle class creation logic
                $class_name = $_POST['name'];
                if (!empty($class_name)) {
                    $sql = "INSERT INTO Classes (class_name) VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $class_name);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "Class name cannot be empty.";
                }
            } elseif ($action === 'edit') {
                // Handle class editing logic
                $class_id = $_POST['class_id'];
                $class_name = $_POST['name'];
                if (!empty($class_id) && !empty($class_name)) {
                    $sql = "UPDATE Classes SET class_name = ? WHERE class_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $class_name, $class_id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "Class ID and name cannot be empty.";
                }
            } elseif ($action === 'delete') {
                // Handle class deletion logic
                $class_id = $_POST['class_id'];
                if (!empty($class_id)) {
                    $sql = "DELETE FROM Classes WHERE class_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $class_id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "Class ID cannot be empty.";
                }
            }
        }
        
        
        // Display all classes in a table
        $sql = "SELECT class_id, class_name FROM Classes";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['class_id'] . "</td>";
                echo "<td>" . $row['class_name'] . "</td>";
                echo "<td>";
                // Form for edit action
                echo '<form method="POST" action="" style="display: inline;">';
                echo '<input type="hidden" name="class_id" value="' . $row['class_id'] . '">';
                echo '<input type="text" name="name" value="' . $row['class_name'] . '" required>';
                echo '<button type="submit" name="action" value="edit">Edit</button>';
                echo '</form>';
                
                // Form for delete action
                echo '<form method="POST" action="" style="display: inline;">';
                echo '<input type="hidden" name="class_id" value="' . $row['class_id'] . '">';
                echo '<button type="submit" name="action" value="delete">Delete</button>';
                echo '</form>';
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
    
    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
