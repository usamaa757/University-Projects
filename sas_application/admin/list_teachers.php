<?php
include "header.php";
include '../other/db_connection.php';

// Fetch teachers with 'pending' status from the `teachers` table
$query = "SELECT * FROM teachers WHERE status = 'pending'";
$result = $conn->query($query);

// Initialize an empty array to hold teacher data
$teachers = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row; // Store each row of teacher data in the array
    }
} else {
    $noDataMessage = "No pending teacher registrations found.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>teacher Registrations</title>
    <!-- Link to the CSS file -->
    <link rel="stylesheet" href="../css/form.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b5dd7c;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 40px;
        }

        h1 {
            text-align: center;
            color: #333333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #dddddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            text-align: center;
            color: #888888;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Teacher Registrations</h1>

        <!-- Display the table if there are teachers with pending status -->
        <?php if (!empty($teachers)): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($teacher['teacher_id']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['teacher_name']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                       
                        <td><?php echo htmlspecialchars($teacher['status']); ?></td>
                        <td>
                            <a href="../admin/update_teacher_status.php?teacher_id=<?php echo $teacher['teacher_id']; ?>&status=approved">Approve</a> |
                            <a href="../admin/update_teacher_status.php?teacher_id=<?php echo $teacher['teacher_id']; ?>&status=rejected">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p><?php echo $noDataMessage; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>
