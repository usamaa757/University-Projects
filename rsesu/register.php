<?php
$host = 'localhost';
$db = 'supervisor_portal';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function isValidName($name)
{
    return preg_match("/^[a-zA-Z ]{3,30}$/", $name);
}


$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["fullname"]);
    $affiliation = trim($_POST["affiliation"]);
    $email = trim($_POST["email"]);
    $expertise = isset($_POST["expertise"]) ? implode(", ", $_POST["expertise"]) : '';
    $publications = intval($_POST["publications"]);

    if (empty($name) || empty($affiliation) || empty($email) || empty($expertise) || $publications < 0) {
        $error = "All fields are required.";
    } elseif (!isValidName($name)) {
        $error = "Invalid name. Only letters and spaces (3–30 characters).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {



        $sql = "INSERT INTO supervisors (fullname, affiliation, email, expertise, publications)
        VALUES ('$name', '$affiliation', '$email', '$expertise', $publications)";

        if ($conn->query($sql)) {
            $success = "<p>Supervisor Registered Successfully.</p><a href='supervisors.php'>View All</a>";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supervisor Registration - BC210409630</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Supervisor Research Portal - BC210409630</h1>
        <div class="nav-buttons">
            <a href="index.php">Home</a>
            <a href="register.php">Supervisor Registration</a>
            <a href="supervisors.php">Supervisors List</a>
        </div>
    </header>



    <div class="container">
        <h2>Supervisor Registration</h2>
        <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" name="fullname" id="fullname" required>

            <label for="affiliation">Current Affiliation:</label>
            <input type="text" name="affiliation" id="affiliation" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label>Area of Expertise:</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="expertise[]" value="AI"> AI</label>
                <label><input type="checkbox" name="expertise[]" value="Data Science"> Data Science</label>
                <label><input type="checkbox" name="expertise[]" value="Cybersecurity"> Cybersecurity</label>
                <label><input type="checkbox" name="expertise[]" value="IoT"> IoT</label>
                <label><input type="checkbox" name="expertise[]" value="Software Engineering"> Software
                    Engineering</label>
            </div>

            <label for="publications">Number of Publications:</label>
            <input type="number" name="publications" id="publications" min="0" required>

            <div class="buttons">
                <input type="submit" value="Submit">
                <input type="reset" value="Clear All">
            </div>
        </form>
    </div>
</body>

</html>