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

// Get the student ID from the URL
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

// Fetch the student record from the stud_admission table
$student_query = "
    SELECT *
    FROM stud_admission
    WHERE student_id = ?
";

$student_stmt = $conn->prepare($student_query);
$student_stmt->bind_param('i', $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$row = $student_result->fetch_assoc();

// Check if student record was found
if (!$row) {
    echo "No student found with the provided ID.";
    exit();
}

// Fetch education details
$education_query = "
    SELECT *
    FROM education
    WHERE student_id = ?
";

$education_stmt = $conn->prepare($education_query);
$education_stmt->bind_param('i', $student_id);
$education_stmt->execute();
$education_result = $education_stmt->get_result();

// Fetch mark sheets
$marksheets_query = "
    SELECT *
    FROM student_marksheets
    WHERE student_id = ?
";

$marksheets_stmt = $conn->prepare($marksheets_query);
$marksheets_stmt->bind_param('i', $student_id);
$marksheets_stmt->execute();
$marksheets_result = $marksheets_stmt->get_result();

// Process mark sheets into an associative array by education_id
$marksheets_by_edu = [];
while ($marksheet = $marksheets_result->fetch_assoc()) {
    $marksheets_by_edu[$marksheet['education_id']][] = $marksheet['marksheet_img'];
}

$student_stmt->close();
$education_stmt->close();
$marksheets_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Your Application Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 70%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            background-color: #f1f1f1;
            padding: 10px;
            margin: 0;
            border-bottom: 2px solid #ddd;
        }

        .section table {
            width: 100%;
            border-collapse: collapse;
        }

        .section table th,
        .section table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .section table th {
            background-color: #f1f1f1;
        }

        .button-container {
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            margin: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-edit {
            background-color: #007bff;
        }

        .btn-back {
            background-color: #6c757d;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>View Your Application</h1>

        <!-- Personal Information Section -->
        <div class="section">
            <h3>Personal Information</h3>
            <table>
                <tr>
                    <th>CNIC</th>
                    <td><?php echo htmlspecialchars($row['cnic']); ?></td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td><?php echo htmlspecialchars($row['dob']); ?></td>
                </tr>
                <tr>
                    <th>Nationality</th>
                    <td><?php echo htmlspecialchars($row['nationality']); ?></td>
                </tr>
                <tr>
                    <th>Profile Pic</th>
                    <td><a href="<?php echo htmlspecialchars($row['photograph']); ?>" target="_blank">View</a></td>
                </tr>
            </table>
        </div>

        <!-- Contact Information Section -->
        <div class="section">
            <h3>Contact Information</h3>
            <table>
                <tr>
                    <th>Country</th>
                    <td><?php echo htmlspecialchars($row['country']); ?></td>
                </tr>
                <tr>
                    <th>City</th>
                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                </tr>
                <tr>
                    <th>Postal Address</th>
                    <td><?php echo htmlspecialchars($row['postal_address']); ?></td>
                </tr>
                <tr>
                    <th>Residential Address</th>
                    <td><?php echo htmlspecialchars($row['residential_address']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Education Section -->
        <div class="section">
            <h3>Education</h3>
            <?php if ($education_result->num_rows > 0): ?>
                <?php while ($edu_row = $education_result->fetch_assoc()): ?>
                    <table>
                        <tr>
                            <th>Degree</th>
                            <td><?php echo htmlspecialchars($edu_row['qualification']); ?></td>
                        </tr>
                        <tr>
                            <th>Institute Name</th>
                            <td><?php echo htmlspecialchars($edu_row['institute_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Passing Year</th>
                            <td><?php echo htmlspecialchars($edu_row['passing_year']); ?></td>
                        </tr>
                        <tr>
                            <th>Grade</th>
                            <td><?php echo htmlspecialchars($edu_row['grade']); ?></td>
                        </tr>
                        <tr>
                            <th>Obtained Marks</th>
                            <td><?php echo htmlspecialchars($edu_row['obtained_marks']); ?></td>
                        </tr>
                        <tr>
                            <th>Total Marks</th>
                            <td><?php echo htmlspecialchars($edu_row['total_marks']); ?></td>
                        </tr>
                        <tr>
                            <th>Mark Sheet</th>
                            <td>
                                <?php if (isset($marksheets_by_edu[$edu_row['education_id']])): ?>
                                    <?php foreach ($marksheets_by_edu[$edu_row['education_id']] as $marksheet): ?>
                                        <a href="<?php echo htmlspecialchars($marksheet); ?>" target="_blank">View Mark Sheet</a><br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    No mark sheet available
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    <br>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No education records found.</p>
            <?php endif; ?>
        </div>

        <!-- Status Section -->
        <div class="section">
            <table>
                <tr>
                    <th>Status</th>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Back Button -->
        <div class="button-container">
            <a href="admin-dashboard.php" class="btn btn-back">Back</a>
        </div>
    </div>

</body>

</html>
