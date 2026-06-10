<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: user-login.php");
    exit();
}

// Include the database connection file
include 'db_connect.php';

// Retrieve the user ID from the session
$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['full_name'];

// Fetch the student's admission form data
$query = "
    SELECT sa.*, 
           ed.qualification,
           ed.institute_name,
           ed.passing_year,
           ed.grade,
           ed.obtained_marks,
           ed.total_marks
    FROM stud_admission sa
    LEFT JOIN education ed ON sa.student_id = ed.student_id
    WHERE sa.student_id = ? AND sa.status IN ('pending', 'approved', 'rejected')
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['status'];
    $remarks = $row['remarks'] ?? ''; // Use null coalescing operator to handle case where remarks might be null
} else {
    echo '<a href="stud_admission.php">Submit form</a>' . "<br>";
    echo "Form is not submitted yet by: " . '<strong>' . htmlspecialchars($student_name) . '</strong>';
    exit();
}

// Fetch all unique education records for the student
$education_query = "
    SELECT ed.education_id,
           ed.qualification,
           ed.institute_name,
           ed.passing_year,
           ed.grade,
           ed.obtained_marks,
           ed.total_marks
    FROM education ed
    WHERE ed.student_id = ?
    GROUP BY ed.education_id
";

$education_stmt = $conn->prepare($education_query);
$education_stmt->bind_param('i', $student_id);
$education_stmt->execute();
$education_result = $education_stmt->get_result();

// Fetch mark sheets for the student
$marksheets_query = "
    SELECT ms.education_id,
           ms.marksheet_img
    FROM student_marksheets ms
    WHERE ms.student_id = ?
";

$marksheets_stmt = $conn->prepare($marksheets_query);
$marksheets_stmt->bind_param('i', $student_id);
$marksheets_stmt->execute();
$marksheets_result = $marksheets_stmt->get_result();

// Organize mark sheets by education ID
$marksheets_by_edu = [];
while ($marksheet = $marksheets_result->fetch_assoc()) {
    $education_id = $marksheet['education_id'];
    if (!isset($marksheets_by_edu[$education_id])) {
        $marksheets_by_edu[$education_id] = [];
    }
    $marksheets_by_edu[$education_id][] = $marksheet['marksheet_img'];
}

$stmt->close();
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
            <table>
                <tr>
                    <th>Degree</th>
                    <td><?php echo htmlspecialchars($row['degree']); ?></td>
                </tr>
                <tr>
                    <th>Program</th>
                    <td><?php echo htmlspecialchars($row['program']); ?></td>
                </tr>
            </table>
        </div>
        <div class="button-container">
            <?php if ($status === 'pending'): ?>
                <a href="edit_personal_info.php" class="btn btn-edit">Edit Personal Info</a>
            <?php endif; ?>
            <a href="stud_dashboard.php" class="btn btn-back">Back</a>
        </div>
        <br>
        <!-- Qualification Section -->
        <div class="section">
            <h3>Qualification</h3>
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
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                    </table>
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
                    <td><?php echo htmlspecialchars($status); ?></td>
                </tr>
                <?php if ($status === 'rejected'): ?>
                    <tr>
                        <th>Remarks</th>
                        <td><?php echo htmlspecialchars($remarks); ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Buttons -->
        <div class="button-container">
            <?php if ($status === 'pending'): ?>
                <a href="edit_education_info.php" class="btn btn-edit">Edit Education Info</a>
            <?php endif; ?>
            <a href="stud_dashboard.php" class="btn btn-back">Back</a>
        </div>
    </div>

</body>

</html>
