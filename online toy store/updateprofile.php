<?php require('header.php');
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
} ?>
<script>
    function myFunction() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
<div class="container">
<div class="container col-lg-9 justify-content-center align-items-center">
    <div><h2>My Profile</h2></div>
    <hr>
</div>
<?php
require('connectToMysql.php');
$userid = $_SESSION['userid'];
$search = "SELECT * FROM users WHERE user_id='$userid'";
$res = mysqli_query($connecti, $search);

if ($row = mysqli_fetch_assoc($res)) { ?>
    <br><br>
    <div class="container col-lg-9 justify-content-center align-items-center">
        <form method="post" action="uploadprofile.php" enctype="multipart/form-data">
            <div class="col-lg-6 form-group d-grid gap-2">
                <table>
                    <tr>
                        <td><input type="hidden" name="user_id" class="form-control" value="<?php echo $row['user_id']; ?>" /></td>
                    </tr>
                    <tr>
                        <th>User Name:</th>
                        <td><input type="text" name="username" class="form-control" value="<?php echo $row['username']; ?>" /></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><input type="text" name="email" class="form-control" value="<?php echo $row['email']; ?>" /></td>
                    </tr>
                    <tr>
                        <th>Password:</th>
                        <td><input id="password" type="password" name="password" class="form-control" value="<?php echo $_SESSION["uid"]; ?>">
                            <input type="checkbox" onclick="myFunction()"> Show password
                        </td>
                    <tr>
                        <th>Contact:</th>
                        <td><input type="number" name="contact" class="form-control" value="<?php echo $row['contact']; ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" class="btn btn-outline-primary me-2" name="submit" value="Update"></td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
<?php
} else {
    echo "<br><br><h5>Error!</h5>";
}
?>

<?php require('footer.php'); ?>