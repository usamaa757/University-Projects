<?php

include 'connection.php';
include "header/header.php";
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $query = "select * from admin where email='$email' && password='$password'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['id']          = $row['id'];

        echo "<script>window.location='Admin/Dashboard.php';alert('Login Successfully');</script>";
    } else {
        echo "<script>window.location='Admin_Login.php';alert('Invalid Email OR Password');</script>";
    }
}
?>


<br><br>

<div class="container p-3 my-3 bg-info" style="border-radius:15px; width:40%">
    <br>
    <h2 style="color:black;text-align:center;">Admin Login</h2>
    <br>




    <form method="post">
        <center>


            <div class="form-group mb-4">
                <input type="text" name="email" placeholder="Email" class="form-control w-50" required>

            </div>

            <div class="form-group mb-4">
                <input type="password" name="password" placeholder="Password" class="form-control w-50" required>

            </div>

            <div class="form-group mb-4">
                <input type="submit" name="submit" class="btn btn-success w-50" value="Login">
            </div>

        </center>
    </form>
</div>
</div>
</body>

</html>