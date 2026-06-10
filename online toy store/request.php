<?php require('header.php'); 
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
}?>
<div class="container col-lg-6 justify-content-center align-items-center">
    <div><h2>Request A Toy</h2></div>
    <hr>
    <form action="#" method="post" enctype="multipart/form-data" class="d-grid gap-2">
        <div class="col-lg-12 form-group ">
            <table class="" >
                
                <tr>
                    <th>Toy Name:</th>
                    <td><input type="text" name="name" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Toy Picture:</th>
                    <td><input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Toy Description:</th>
                    <td><input type="text" name="toydesc" class="form-control" required></td>
                </tr><tr>
                    <td></td>
                    <td><button type="submit" name="submit" onclick="this.form.submit()" class="btn btn-sm btn-outline-primary me-2">Send Request</button></td>
                </tr>
            </table>
        </div>
    </form>
</div>
<?php
require('connectToMysql.php');
$user = $_SESSION['userid'];
$search = "SELECT * FROM requests WHERE user_id=$user";
if($res = mysqli_query($connecti, $search)) {
while($row = mysqli_fetch_assoc($res)) {
     //Display Requests Record Table
     echo '<div class="container col-md-12">
        <hr><br><h2>My Requests</h2><br>
        <form class="row" action="#" method="post">
         <table  class="table table-bordered" align="center" width="50%" border="1">
         <tr>
             <th>Request ID</th>
             <th>Toy Name</th>
             <th>Toy Description</th>
             <th>Toy Picture</th>
             <th>Actions</th>
         </tr>
         <tr> 
             <td><input type=hidden name="reqid" value="' . $row['reqid'] . '">' . $row['reqid'] . '</td>
             <td>' . $row['toyname'] . '</td>
             <td>' . $row['toydesc'] . '</td>
             <td width="25%"><img src="../' . $row['toypicture'] . '" width="150" height="150"></td>
             <td><button type="submit" value="" class="btn btn-danger me-2" required>Delete</button></td>
         </tr>
     </table>
     </form></div>';}
 } else {
     echo '<br><div class="container col-md-12"><h5>No requests found.</h5></div>';
 }
if (isset($_POST["submit"])) {
$target_dir = "C:/xampp/htdocs/project2/public/images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or not
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        //"File is an image"
        $uploadOk = 1;
    } else {
        echo "File is not an image." . $check["mime"] . ".";
        $uploadOk = 0;
    }
}
if ($uploadOk == 1) {
    $toyname = $_POST['name'];
    $image = basename($_FILES["fileToUpload"]["name"]);
    $desc = $_POST['toydesc'];

    $sql = "INSERT INTO requests(toyname, toypicture, toydesc) VALUES ( '$toyname',  'images/$image', '$desc')";
    
    if (mysqli_query($connecti, $sql)) {
        header('Refresh:3; url=request.php');
        echo '<script>alert("Request Sent");</script>';
    } else {
        header('Refresh:3; url=request.php');
        echo '<script>alert("Error sending request");</script>';
    }
} else {
    echo '<script>alert("Upload Image Error");</script>';
}
}
?>
<?php require('footer.php'); ?>