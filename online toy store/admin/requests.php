<?php require('../header.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container col-lg-12 justify-content-center align-items-center">
    <div>
        <br><br>
        <h2>All Requests</h2>
    </div>
    <?php
    require('../connectToMysql.php');
    $search = "SELECT * FROM requests";
    if ($res = mysqli_query($connecti, $search)) {
        while ($row = mysqli_fetch_assoc($res)) {
            //Display Requests Record Table
            echo '<div class="container col-lg-12">
        <br><br>
        <form class="row" action="#" method="post">
         <table  class="table table-bordered" align="center" width="50%" border="1">
         <tr>
             <th>Request ID</th>
             <th>Toy Name</th>
             <th>Toy Description</th>
             <th>Toy Picture</th>
             <th>Feedback</th>
         </tr>
         <tr> 
             <td><input type=hidden name="reqid" value="' . $row['reqid'] . '">' . $row['reqid'] . '</td>
             <td>' . $row['toyname'] . '</td>
             <td>' . $row['toydesc'] . '</td>
             <td width="25%"><img src="../' . $row['toypicture'] . '" width="150" height="150"></td>
             <td><input type="text" name="feedback" class="form-control" value="' . $row['feedback'] . '" />
             <button type="submit" class="btn btn-danger me-2" name="fb" onclick="this.form.submit();" required>Send</button></td>
         </tr>
     </table>
     </form></div>';
        }
    } else {
        echo '<br><div class="container col-md-12"><h5>No requests found.</h5></div>';
    }
    ?>

</div>
<?php
if (isset($_POST['fb'])) {
    $reqid = $_POST['reqid'];
    $fb = $_POST['feedback'];

    $sql = "UPDATE requests SET feedback='$fb' WHERE reqid='$reqid' ";

    if (mysqli_query($connecti, $sql)) {
        header('Refresh:0; url=requests.php');
        echo '<script>alert("Feedback Sent!");</script>';
    } else {
        header('Refresh:0; url=requests.php');
        echo '<script>alert("Failed to send feedback!");</script>';
    }
}?>
<?php require('../footer.php'); ?>