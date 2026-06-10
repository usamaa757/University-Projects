<?php
include 'header.php';
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User is not logged in!'); window.location='login.php';</script>";
    exit;
}
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

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO job_seekers (job_seeker_id, name, email, contact, location, resume, education, experience) 
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
                        <button type="submit" class="btn btn-success w-100">Save Profile</button>
                    </form>
                </div>
            </div>
        </div>

        </body>

        </html>