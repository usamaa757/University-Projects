<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b5dd7c;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #0f582d;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .container {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }
        .box {
            background-color: #fff;
            padding: 15px;
            width: 28%;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .box header {
            background-color: #0f582d;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }
        a {
            color: #0f582d;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Total Records Overview</h1>
    </div>

    <?php
    include_once '../other/db_connection.php';
    include_once 'header.php';

    // Function to get total count from a table based on status
    function getTotalCount($conn, $tableName, $status = null) {
        $sql = "SELECT COUNT(*) AS total FROM $tableName";
        if ($status !== null) {
            $sql .= " WHERE status = '$status'";
        }

        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        } else {
            echo "Error fetching count from $tableName: " . $conn->error;
            return 0;
        }
    }

    // Fetch total counts based on status
    $totalTeachers = getTotalCount($conn, 'teachers', 'approved'); // Enrolled teachers
    $totalTeacherRegistrations = getTotalCount($conn, 'teachers', 'pending'); // Registered teachers
    $totalParents = getTotalCount($conn, 'parents', 'approved'); // Enrolled parents
    $totalParentRegistrations = getTotalCount($conn, 'parents', 'pending'); // Registered parents
    $totalStudents = getTotalCount($conn, 'students', 'approved'); // Enrolled students
    $totalStudentRegistrations = getTotalCount($conn, 'students', 'pending'); // Registered students

    // Close connection
    $conn->close();
    ?>

    <div class="container">
        <div class="box">
            <header>
                <h2>Teachers</h2>
            </header>
            <ul>
                <li>Enrolled Teachers: <?php echo $totalTeachers; ?></li>
                <li>Registered Teachers: <?php echo $totalTeacherRegistrations; ?></li>
                <li><a href="view_teachers.php">View Enrolled Teachers</a></li>
                <li><a href="list_teachers.php">View Teacher Registrations</a></li>
            </ul>
        </div>

        <div class="box">
            <header>
                <h2>Students</h2>
            </header>
            <ul>
                <li>Enrolled Students: <?php echo $totalStudents; ?></li>
                <li>Registered Students: <?php echo $totalStudentRegistrations; ?></li>
                <li><a href="view_students.php">View Enrolled Students</a></li>
                <li><a href="list_students.php">View Student Registrations</a></li>
            </ul>
        </div>

        <div class="box">
            <header>
                <h2>Parents</h2>
            </header>
            <ul>
                <li>Enrolled Parents: <?php echo $totalParents; ?></li>
                <li>Registered Parents: <?php echo $totalParentRegistrations; ?></li>
                <li><a href="view_parents.php">View Enrolled Parents</a></li>
                <li><a href="list_parents.php">View Parent Registrations</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
