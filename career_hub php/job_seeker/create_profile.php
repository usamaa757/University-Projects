<?php
include 'header.php';
include '../db_connect.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User is not logged in!'); window.location='login.php';</script>";
    exit;
}
$sql = "SELECT * FROM job_seeker_profile WHERE job_seeker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
if ($profile) {
    echo "<script>alert('Profile already exists!'); window.location='profile.php';</script>";
    exit;
}
$stmt->close();

// Create Job Seeker Profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $location = $_POST['location'];
    $education = $_POST['education'];
    $experience = $_POST['experience'];

    // Resume Upload Handling
    $resume = "";
    if (!empty($_FILES["resume"]["name"])) {
        $allowed_types = ['pdf', 'doc', 'docx'];
        $file_ext = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $target_dir = "uploads/";
            $resume = $target_dir . time() . "_" . basename($_FILES["resume"]["name"]);
            move_uploaded_file($_FILES["resume"]["tmp_name"], $resume);
        } else {
            echo "<script>alert('Invalid file format! Only PDF, DOC, DOCX allowed.');</script>";
            exit;
        }
    }
    // Check if contact already exists
    $check_sql = "SELECT contact FROM job_seeker_profile WHERE contact = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $contact);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Error: Contact Number already exists!'); window.location='create_profile.php';</script>";
        exit();
    }

    // Close the first statement before checking email
    $check_stmt->close();

    // Check if email already exists
    $check_sql = "SELECT email FROM job_seeker_profile WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Error: Email already exists!'); window.location='create_profile.php';</script>";
        exit();
    }

    $check_stmt->close();


    // Insert into database
    $stmt = $conn->prepare("INSERT INTO job_seeker_profile (job_seeker_id, name, email, contact, location, resume, education, experience) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_id, $name, $email, $contact, $location, $resume, $education, $experience);

    if ($stmt->execute()) {
        echo "<script>alert('Profile Created Successfully!'); window.location='profile.php';</script>";
    } else {
        error_log("SQL Error: " . $stmt->error); // Log the error for debugging
        echo "<script>alert('Error creating profile.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Job Seeker Profile</h4>
                </div>
                <div class="card-body">
                    <form action="create_profile.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact" class="form-control" maxlength="11" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Education</label>
                            <textarea name="education" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Experience</label>
                            <textarea name="experience" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Resume (PDF or Word)</label>
                            <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                        <div class="text-center">

                            <button type="submit" class="btn btn-outline-dark">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        </body>

        </html>