<?php
include '../header/superintendent-header.php';


include "../connection.php";

if (isset($_POST['submit'])) {
    $id             = $_POST['id'];
    $name           = mysqli_real_escape_string($con, $_POST['name']);
    $email          = mysqli_real_escape_string($con, $_POST['email']);
    $phone          = mysqli_real_escape_string($con, $_POST['phone']);
    $city           = mysqli_real_escape_string($con, $_POST['city']);
    $address        = mysqli_real_escape_string($con, $_POST['address']);
    $center_pref    = mysqli_real_escape_string($con, $_POST['center_pref']);
    $availability   = mysqli_real_escape_string($con, $_POST['availability']);
    $qualification  = mysqli_real_escape_string($con, $_POST['qualification']);
    $password       = mysqli_real_escape_string($con, $_POST['password']);

    // SQL query - remove extra comma
    $query = "UPDATE user SET 
                name='$name',
                email='$email',
                phone='$phone',
                city='$city',
                address='$address',
                center_pref='$center_pref',
                availability='$availability',
                qualification='$qualification',
                password='$password'
              WHERE id='$id'";

    $update = mysqli_query($con, $query);

    if ($update) {
        echo "<script>alert('Profile Updated Successfully'); window.location='update_profile.php';</script>";
    } else {
        echo "<script>alert('Update Failed'); window.location='update_profile.php';</script>";
    }
}

// Fetch logged-in user data
$id = $_SESSION['id'];
$query = "SELECT * FROM user WHERE id='$id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
?>

<div class="container p-4 my-4 bg-info" style="border-radius:15px; width:40%">
    <div class="card shadow-lg p-4" style="max-width: 600px; margin:auto; border-radius:15px;">
        <h2 class="text-center text-primary mb-4">Update Profile</h2>

        <form method="POST">

            <input type="hidden" name="id" value="<?= $row['id']; ?>">

            <!-- Employee ID (readonly) -->
            <div class="form-group">
                <label>Employee ID</label>
                <input type="text" class="form-control" value="<?= $row['employee_id']; ?>" readonly>
            </div>



            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?= $row['name']; ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $row['email']; ?>" required>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= $row['phone']; ?>" required>
            </div>

            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?= $row['city']; ?>" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" value="<?= $row['address']; ?>" required>
            </div>

            <div class="form-group">
                <label>Center Preference</label>
                <input type="text" name="center_pref" class="form-control" value="<?= $row['center_pref']; ?>" required>
            </div>

            <div class="form-group">
                <label>Availability</label>
                <input type="text" name="availability" class="form-control" value="<?= $row['availability']; ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Qualification</label>
                <input type="text" name="qualification" class="form-control" value="<?= $row['qualification']; ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="text" name="password" class="form-control" value="<?= $row['password']; ?>" required>
            </div>

            <div class="text-center">
                <input type="submit" name="submit" class="btn btn-success w-50" value="Update">
            </div>

        </form>
    </div>
    </body>

    </html>