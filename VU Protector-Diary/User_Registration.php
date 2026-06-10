<?php
include 'connection.php';

if (isset($_POST['submit'])) {
    $employee_id   = $_POST['employee_id'];
    $role          = $_POST['role'];
    $name          = $_POST['name'];
    $email         = $_POST['email'];
    $phone         = $_POST['phone'];
    $city          = $_POST['city'];
    $address       = $_POST['address'];
    $center_pref   = $_POST['center_pref'];
    $availability  = $_POST['availability'];
    $qualification = $_POST['qualification'];
    $password      = $_POST['password'];


    $sql = "INSERT INTO user
    (employee_id, role, name, email, phone, city, address, center_pref, availability, qualification, password ) 
    VALUES 
    ('$employee_id','$role','$name','$email','$phone','$city','$address','$center_pref','$availability','$qualification','$password')";

    $run = mysqli_query($con, $sql);

    if ($run) {
        echo "<script>alert('Registered Successfully');window.location='User_Login.php';</script>";
    } else {
        echo "<script>alert('Invalid Data');window.location='User_Registration.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Registration - VU Proctors Diary</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include "header/header.php"; ?>
    <br><br>

    <div class="container p-4 my-4 bg-info" style="border-radius:15px; width:40%">
        <h2 class="text-center text-dark">User Registration</h2>
        <br>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="employee_id" placeholder="Employee ID" class="form-control" required>
            </div>

            <div class="form-group">
                <select name="role" class="form-control" required>
                    <option value="">-- Select Role --</option>
                    <option value="Superintendent">Superintendent</option>
                    <option value="Invigilator">Invigilator</option>
                </select>
            </div>

            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="number" name="phone" placeholder="Phone No" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="text" name="city" placeholder="City" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="text" name="address" placeholder="Address" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="text" name="center_pref" placeholder="Center Preferences" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="text" name="availability" placeholder="Availability (e.g. Morning, Evening, Dates)"
                    class="form-control" required>
            </div>

            <div class="form-group">
                <input type="text" name="qualification" placeholder="Qualification" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" class="form-control" required>
            </div>

            <div class="form-group text-center">
                <input type="submit" name="submit" class="btn btn-success w-50" value="Register">
            </div>
        </form>
    </div>

</body>

</html>