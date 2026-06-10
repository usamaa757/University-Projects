<?php
include '../other/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $class_id = $_POST['class_id'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the database
    $sql = "INSERT INTO students (student_name, dob, class_id, gender, email, password)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssisss', $name, $dob, $class_id, $gender, $email, $hashed_password);

    if ($stmt->execute()) {
        $message = "Registration complete! We are currently reviewing your details and will be in touch with you soon. Thank you for waiting.";
    } else {
        $message = "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the form with the message
    header("Location: student_reg.php?message=" . urlencode($message));
    exit;
}

// Fetch classes from the database
$class_sql = "SELECT class_id, class_name FROM classes";
$class_result = $conn->query($class_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Layout | School Automation System</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
 
        <nav>
            <div class="menu">
                <div class="logo">
                    <a href="#"><i class="fas fa-school"></i> School Automation System</a>
                </div>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../other/about.php">About</a></li>
                    <li><a href="../other/contact.php">Contact</a></li>
                    <li><a href="../other/login.php">Login</a></li>
                    <li><a href="../other/register.php">Register</a></li>
                </ul>
            </div>
        </nav>

    <br><br><br>
    <div class="container">
        <h2>Please fill out this form</h2>

        <!-- Display the message if it exists -->
        <?php if (isset($_GET['message'])): ?>
            <p style="text-align: center; color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required><br><br>

            <label for="class_id">Select Class:</label>
            <select id="class_id" name="class_id" required>
                <?php if ($class_result->num_rows > 0): ?>
                    <?php while ($class_row = $class_result->fetch_assoc()): ?>
                        <option value="<?php echo $class_row['class_id']; ?>">
                            <?php echo htmlspecialchars($class_row['class_name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No classes available</option>
                <?php endif; ?>
            </select><br><br>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>

</html>