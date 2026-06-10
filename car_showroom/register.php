<?php
include 'header.php';
include "db.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get input
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $phone = trim($_POST["phone"]);
    $personal_bank_name = $_POST["personal_bank_name"];
    $personal_account_number = $_POST["personal_account_number"];
    $guarantor_name = $_POST["guarantor_name"];
    $guarantor_bank_name = $_POST["guarantor_bank_name"];
    $guarantor_account_number = $_POST["guarantor_account_number"];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='register.php';</script>";
        exit;
    }

    // Check if email or phone already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $check->bind_param("ss", $email, $phone);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email or Phone already exists!'); window.location.href='register.php';</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle profile picture upload
    $profile_pic = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $targetDir = "images/profile_pic/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["profile_pic"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedTypes)) {
            $newFileName = uniqid() . "." . $fileExt;
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
                $profile_pic = $targetFilePath;
            } else {
                echo "<script>alert('Failed to upload profile picture!'); window.location.href='register.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed.'); window.location.href='register.php';</script>";
            exit;
        }
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO users 
        (name, email, phone, password_hash, profile_pic, personal_bank_name, personal_account_number, guarantor_name, guarantor_bank_name, guarantor_account_number) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssssssssss",
        $name,
        $email,
        $phone,
        $hashed_password,
        $profile_pic,
        $personal_bank_name,
        $personal_account_number,
        $guarantor_name,
        $guarantor_bank_name,
        $guarantor_account_number
    );
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<script>alert('Registration successful!'); window.location.href='register.php';</script>";
        exit;
    } else {
        $stmt->close();
        $conn->close();
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='register.php';</script>";
        exit;
    }
}

?>


<!-- Registration Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-3">
                <h4 class="text-center">Register</h4>
                <div class="card-body">


                    <form method="post" enctype="multipart/form-data" class="form">

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" name="phone" id="phone" maxlength="11" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="profile_pic" class="form-label">Profile Picture</label>
                            <input type="file" name="profile_pic" id="profile_pic" class="form-control"
                                accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>


                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                required>
                        </div>

                        <h4>Personal Bank Details</h4>
                        <div class="mb-3">
                            <label for="personal_bank_name" class="form-label">Bank Name</label>
                            <input type="text" name="personal_bank_name" id="personal_bank_name" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="personal_account_number" class="form-label">Account Number</label>
                            <input type="text" name="personal_account_number" id="personal_account_number"
                                class="form-control" required>
                        </div>

                        <h4>Guarantor's Bank Details</h4>
                        <div class="mb-3">
                            <label for="guarantor_name" class="form-label">Guarantor's Name</label>
                            <input type="text" name="guarantor_name" id="guarantor_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="guarantor_bank_name" class="form-label">Bank Name</label>
                            <input type="text" name="guarantor_bank_name" id="guarantor_bank_name" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="guarantor_account_number" class="form-label">Account Number</label>
                            <input type="text" name="guarantor_account_number" id="guarantor_account_number"
                                class="form-control" required>
                        </div>
                        <div class="text-center">

                            <button type="submit" class="btn">Register</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>