<?php
session_start();
// Include database connection
include '../other/db_connection.php';

// Assume student ID is provided via session or form data
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['student_name'];

// Retrieve vouchers and student name for the student
$query = "SELECT * FROM fee_vouchers";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System</title>
    <link rel="stylesheet" href="../css/dashboard.css">

</head>

<body>

    <!-- Top bar -->
    <div class="top-bar">
        <!-- Hamburger menu icon -->
        <div class="menu-icon" onclick="toggleNavbar()">
            &#9776;
        </div>
        <div class="logo"><i class="fas fa-school fa-1x"></i>School Automation System </div>
        <div class="user-info">
            <span style="margin-left: 70px;">Welcome, <?php echo htmlspecialchars($student_name); ?></span>
        </div>
    </div>

    <!-- Vertical Navbar -->
    <div class="navbar" id="navbar">
        <ul>
        <li><a href="student_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="student_dashboard.php"><i class="fas fa-user"></i> Home</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="view_lecture.php"><i class="fas fa-chalkboard-teacher"></i> Lectures</a>
            </li>
            <li><a href="view_voucher.php"><i class="fas fa-money-bill"></i> Fees</a></li>
            <li><a href="view_assignments.php"><i class="fas fa-tasks"></i> Assignments</a></li>
            <li><a href="view_quiz.php"><i class="fas fa-pen"></i> Quizzes</a></li>
            <li><a href="view_attendance.php"><i class="fas fa-graduation-cap"></i> attendance </a></li>
            <li><a href="../other/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>

    </div>


    <script>
        // Function to toggle the navbar
        function toggleNavbar() {
            var navbar = document.getElementById("navbar");
            navbar.classList.toggle("open");
        }
    </script>

    <link rel="stylesheet" href="../css/form.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>

<body>
    <br><br><br>
    <header>
        <h2>My Vouchers</h2>
    </header>

    <table>
        <thead>
            <tr>
                <th>Voucher ID</th>
                <th>Class</th>
                <th>Amount</th>
                <th>Month</th>
                <th>Year</th>
                <th>Issue Date</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['voucher_id']; ?></td>
                <td><?php echo $row['class_id']; ?></td>
                <td><?php echo $row['fee_amount']; ?></td>
                <td><?php echo $row['month']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['issue_date']; ?></td>
                <td><?php echo $row['due_date']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Close statement and connection
$stmt->close();
$conn->close();
?>
