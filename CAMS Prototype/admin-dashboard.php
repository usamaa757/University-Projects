<?php
session_start();
$admin_id = $_SESSION['admin_id'];

// Check if the admin is logged in, otherwise redirect to the login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

// Include the database connection file
include 'db_connect.php';

// Initialize message variable
$msg = '';

// Fetch all admission forms from the database
$query = "
    SELECT student_id, 
           full_name, 
           degree, 
           program, 
           status, 
           remarks
    FROM stud_admission
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Initialize an array to track unique students
$students = [];

// Process results to remove duplicates
while ($row = mysqli_fetch_assoc($result)) {
    $student_id = $row['student_id'];
    if (!isset($students[$student_id])) {
        $students[$student_id] = [
            'student_id' => $row['student_id'],
            'full_name' => $row['full_name'],
            'degree' => $row['degree'],
            'program' => $row['program'],
            'status' => $row['status'],
            'remarks' => $row['remarks']
        ];
    }
}

// Handle Approve, Reject, or Pending actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['student_id'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    if (isset($_POST['approve'])) {
        $query = "UPDATE stud_admission SET status='approved', remarks='$remarks' WHERE student_id=$id";
        mysqli_query($conn, $query);
        echo "<script>alert('Student form approved successfully'); window.location.href='admin-dashboard.php';</script>";
    } elseif (isset($_POST['reject'])) {
        $query = "UPDATE stud_admission SET status='rejected', remarks='$remarks' WHERE student_id=$id";
        mysqli_query($conn, $query);
        echo "<script>alert('Student form rejected successfully'); window.location.href='admin-dashboard.php';</script>";
    } elseif (isset($_POST['pending'])) {
        $query = "UPDATE stud_admission SET status='pending', remarks='$remarks' WHERE student_id=$id";
        mysqli_query($conn, $query);
        echo "<script>alert('Student form status set to pending'); window.location.href='admin-dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="CSS/admin-panel-style.css">
    <style>
        textarea {
            box-sizing: border-box;
        }

        .container {
            width: 90%;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }

        .header h1 {
            margin: 0;
        }

        .header a {
            text-decoration: none;
            color: #333;
            margin: 0 10px;
        }

        .scroll {
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <div>
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div>
                <a style="color: #fff700;" href="admin-profile.php">Profile</a>
                <button style="background-color:red;" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
        <div class="container">
            <h1>Admission Forms</h1>
            <div class="scroll">
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Degree</th>
                        <th>Program</th>
                        <th>View</th>
                        <th>Action</th>
                        <th>Remarks</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($students as $student) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['degree']); ?></td>
                            <td><?php echo htmlspecialchars($student['program']); ?></td>
                            <td>
                                <a href="view_admission.php?student_id=<?php echo urlencode($student['student_id']); ?>">View Details</a>
                            </td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">

                                    <?php if ($student['status'] == 'pending') { ?>
                                        <textarea name="remarks" rows="2" placeholder="Enter remarks here..." required></textarea>
                                        <br>
                                        <button type="submit" name="approve">Approve</button>
                                        <button type="submit" name="reject">Reject</button>
                                        <button type="submit" name="pending">Pending</button>
                                    <?php } else {
                                        echo '-';
                                    } ?>
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($student['remarks']); ?></td>
                            <td><?php echo htmlspecialchars($student['status']); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
