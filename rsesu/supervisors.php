<?php
$host = 'localhost';
$db = 'supervisor_portal';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM supervisors");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supervisor List - BC210409630</title>
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

    <div class="table-container">
        <h2>Registered Supervisors</h2>
        <table>
            <tr>
                <th>Full Name</th>
                <th>Affiliation</th>
                <th>Email</th>
                <th>Expertise</th>
                <th>Publications</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['affiliation']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['expertise']) ?></td>
                <td><?= htmlspecialchars($row['publications']) ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>

</html>