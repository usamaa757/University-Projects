<?php
session_start();
$student_id = $_SESSION['student_id'];

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: user-login.php"); // Redirect to login page if not logged in
    exit();
}

include 'db_connect.php'; // Include your database connection file

// Fetch countries and cities
$sql_country = "SELECT * FROM countries";
$result_country = $conn->query($sql_country);

$sql_city = "SELECT * FROM cities";
$result_city = $conn->query($sql_city);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_id = $_POST['student_id'];
    $cnic = $_POST['cnic'];
    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $nationality = $_POST['nationality'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $postal_address = $_POST['postal_address'];
    $residential_address = $_POST['residential_address'];
    $qualification_matric = $_POST['qualification_matric'];
    $qualification_intermediate = $_POST['qualification_intermediate'];
    
    // Degree and program for intermediate qualification
    $degree = $_POST['degree'];
    $program = $_POST['program'];

    // Validate date of birth
    $birthdate = new DateTime($dob);
    $today = new DateTime('today');
    $age = $birthdate->diff($today)->y;

    if ($age < 18) {
        echo "<script>alert('You must be at least 18 years old.');</script>";
        exit();
    }

    // Validate marks
    $matric_obtained_marks = $_POST['matric_obtained_marks'];
    $matric_total_marks = $_POST['matric_total_marks'];
    $inter_obtained_marks = $_POST['inter_obtained_marks'];
    $inter_total_marks = $_POST['inter_total_marks'];

    if ($matric_obtained_marks > $matric_total_marks) {
        echo "<script>alert('Matric obtained marks cannot be greater than total marks.');</script>";
        exit();
    }

    if ($inter_obtained_marks > $inter_total_marks) {
        echo "<script>alert('Intermediate obtained marks cannot be greater than total marks.');</script>";
        exit();
    }

    // Handle photograph upload
    $photograph_target_dir = 'uploads/';
    $photograph_path = '';
    if ($_FILES['photograph']['error'] == UPLOAD_ERR_OK) {
        $photograph_path = $photograph_target_dir . uniqid() . '_' . basename($_FILES['photograph']['name']);
        if (!move_uploaded_file($_FILES['photograph']['tmp_name'], $photograph_path)) {
            echo "<script>alert('Failed to upload photograph.');</script>";
            exit;
        }
    }

    // SQL query to insert data into stud_admission table
    $sql = "INSERT INTO stud_admission (student_id, cnic, full_name, gender, dob, nationality, country, city, postal_address, residential_address, qualification, photograph, degree, program)
    VALUES ('{$_SESSION['student_id']}', '$cnic', '$full_name', '$gender', '$dob', '$nationality', '$country', '$city', '$postal_address', '$residential_address', '$qualification_intermediate', '$photograph_path', '$degree', '$program')";

    if ($conn->query($sql) === TRUE) {
        $student_id = $conn->insert_id; // Get last inserted student_id
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        exit();
    }

    // Education Details
    $education = [
        [
            'qualification' => $qualification_matric,
            'institute_name' => $_POST['matric_institute'],
            'passing_year' => $_POST['matric_passing_year'],
            'grade' => $_POST['matric_grade'],
            'obtained_marks' => $matric_obtained_marks,
            'total_marks' => $matric_total_marks,
            'degree' => $degree,
            'program' => $program,
            'marksheet_type' => 'Matric'
        ],
        [
            'qualification' => $qualification_intermediate,
            'institute_name' => $_POST['inter_institute'],
            'passing_year' => $_POST['inter_passing_year'],
            'grade' => $_POST['inter_grade'],
            'obtained_marks' => $inter_obtained_marks,
            'total_marks' => $inter_total_marks,
            'degree' => $degree,
            'program' => $program,
            'marksheet_type' => 'Intermediate'
        ]
    ];

    foreach ($education as $edu) {
        // Insert each education row into the `education` table
        $sql = "INSERT INTO education (student_id, qualification, institute_name, passing_year, grade, obtained_marks, total_marks, degree, program)
        VALUES ('{$_SESSION["student_id"]}', '{$edu['qualification']}', '{$edu['institute_name']}', '{$edu['passing_year']}', '{$edu['grade']}', '{$edu['obtained_marks']}', '{$edu['total_marks']}', '{$edu['degree']}', '{$edu['program']}')";

        if ($conn->query($sql) === TRUE) {
            $education_id = $conn->insert_id; // Get last inserted education_id
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Handle file uploads for marksheet images
        $marksheet_path = '';
        if ($edu['marksheet_type'] == 'Matric' && isset($_FILES['matric_mark_sheet'])) {
            $marksheet_img = $_FILES['matric_mark_sheet']['name'];
            $marksheet_target_file = $photograph_target_dir . basename($marksheet_img);
            if (move_uploaded_file($_FILES["matric_mark_sheet"]["tmp_name"], $marksheet_target_file)) {
                $marksheet_path = $marksheet_target_file;
            } else {
                echo "Error uploading Matric marksheet.";
                continue;
            }
        } elseif ($edu['marksheet_type'] == 'Intermediate' && isset($_FILES['inter_mark_sheet'])) {
            $marksheet_img = $_FILES['inter_mark_sheet']['name'];
            $marksheet_target_file = $photograph_target_dir . basename($marksheet_img);
            if (move_uploaded_file($_FILES["inter_mark_sheet"]["tmp_name"], $marksheet_target_file)) {
                $marksheet_path = $marksheet_target_file;
            } else {
                echo "Error uploading Intermediate marksheet.";
                continue;
            }
        }

        // Insert uploaded file paths and type into `student_marksheets` table
        $sql = "INSERT INTO student_marksheets (student_id, education_id, marksheet_img, marksheet_type)
                VALUES ('{$_SESSION["student_id"]}', '$education_id', '$marksheet_path', '{$edu['marksheet_type']}')";

        if ($conn->query($sql) === TRUE) {
            echo "Application submitted successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    echo "<script>
        alert('Form submitted successfully!');
        window.location.href = 'view_your_form.php';
    </script>";
}
?>


<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Admission Form</title>
    <link rel="stylesheet" href="styless.css">
</head>

<body>
    <div class="header">
        <h1>Admission Form</h1>
    </div>
    <form  method="POST" enctype="multipart/form-data">
        <!-- Personal Information Section -->
        <fieldset>
            <legend>Personal Information</legend>
            <label for="cnic">CNIC:</label>
            <input type="text" id="cnic" name="cnic" placeholder="e.g: 12345-6789012-3" pattern="\d{5}-\d{7}-\d" required><br>

            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" placeholder="Enter Your Name" required><br>
            <input type="hidden" name="student_id" id="studnet_id" value="<?php echo $student_id; ?>">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="">--Select--</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select><br>
            
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required><br>
            
            <label for="nationality">Nationality:</label>
            <select id="nationality" name="nationality" required>
                <option value="">--Select--</option>
                <option value="pakistani">Pakistani</option>
                <option value="international">International</option>
                <option value="other">Other</option>
            </select><br>
        </fieldset>

        <!-- Contact Information Section -->
        <fieldset>
            <legend>Contact Information</legend>
            <label for="country">--Select Country--</label>
            <select name="country" id="country">
                <option value="">--Select Country--</option>
                <?php
                if ($result_country->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result_country->fetch_assoc()) {
                        echo '<option value="' . $row['country_name'] . '">' . $row['country_name'] . '</option>';
                    }
                } else {
                    echo '<option value="">No countries available</option>';
                }
                ?>
            </select><br>
            <label for="city">--Select City--</label>
            <select name="city" id="city">
                <option value="">Select City</option>
                <?php
                if ($result_city->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result_city->fetch_assoc()) {
                        echo '<option value="' . $row['city_name'] . '">' . $row['city_name'] . '</option>';
                    }
                } else {
                    echo '<option value="">No countries available</option>';
                }
                ?>
            </select><br>
            
            <label for="postal_address">Postal Address:</label>
            <input type="number" id="postal_address" name="postal_address" required><br>
            
            <label for="residential_address">Residential Address:</label>
            <input type="text" id="residential_address" name="residential_address" required><br>
        </fieldset>

        <!-- Select degree Section -->
        <fieldset>
            <legend>Program Selection</legend>
            <label for="degree">*Degree:</label>
            <select id="degree" name="degree" required>
                <option value="">--Select--</option>
                <option value="B.ED">B.ED</option>
                <option value="BS">BS</option>
                <option value="Diploma">Diploma</option>
                <option value="MS">MS</option>

            </select><br>

            <label for="program">*Program:</label>
            <select id="program" name="program" required>
                <option value="">--Select--</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Information Technology">Information Technology</option>
                <option value="Mass Communication">Mass Communication</option>
                <option value="Bussiness Administration">Bussiness Administration</option>
                <option value="Psychology">Psychology</option>
                <option value="Software Engineering">Software Engineering</option>
                <option value="Economics">Economics</option>
                <option value="Mathematics">Mathematics</option>
            </select><br>
        </fieldset>
        
        <!-- Education Section -->
        <fieldset>
    <legend>Education</legend>
    <div id="education-section">
        <div class="education-row">
            <select name="qualification_matric" required>
                <option value="">--Select--</option>
                <option value="Matric">Matric</option>
                <option value="Intermediate">Intermediate</option>
            </select>
            <input type="text" name="matric_institute" placeholder="Institute Name" required>
            <input type="number" name="matric_passing_year" placeholder="Passing Year" required>
            <input type="text" name="matric_grade" placeholder="Grade" required>
            <input type="number" name="matric_obtained_marks" placeholder="Obtained Marks" required>
            <input type="number" name="matric_total_marks" placeholder="Total Marks" required>
        </div>

        <div class="education-row">
            <select name="qualification_intermediate" required>
                <option value="">--Select--</option>
                <option value="Matric">Matric</option>
                <option value="Intermediate">Intermediate</option>
            </select>
            <input type="text" name="inter_institute" placeholder="Institute Name" required>
            <input type="number" name="inter_passing_year" placeholder="Passing Year" required>
            <input type="text" name="inter_grade" placeholder="Grade" required>
            <input type="number" name="inter_obtained_marks" placeholder="Obtained Marks" required>
            <input type="number" name="inter_total_marks" placeholder="Total Marks" required>
        </div>
    </div>
</fieldset>


        <!-- Upload Document Section -->
        <fieldset>
            <legend>Upload Documents</legend>
            <label for="photograph">Photograph:</label>
            <input type="file" id="photograph" name="photograph" accept="image/*" required><br>
            
            <label for="matric_mark_sheet">Matriculation Mark Sheet:</label>
            <input type="file" id="matric_mark_sheet" name="matric_mark_sheet" accept="image/*" required><br>
            
            <label for="inter_mark_sheet">Intermediate Mark Sheet:</label>
            <input type="file" id="inter_mark_sheet" name="inter_mark_sheet" accept="image/*" required><br>
        </fieldset>

        <button type="submit">Submit Application</button>
    </form>
    
    <script src="JavaScript/validations.js"></script>
    <script src="JavaScript/dob.js"></script>
</body>

</html>
