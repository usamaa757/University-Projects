<?php require('../header.php');
require('../connectToMysql.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container col-lg-9 justify-content-center align-items-center">
    <div>
        <br><br>
        <h2>Delete Toys</h2>
    </div>

    <hr>
    <div class="col-lg-6">
        <form method="post" action="" class="d-grid gap-2">
            <table>
                <tr>
                    <th>Toy ID:</th>
                    <td><input type="text" name="toyid" formmethod="post" class="form-control" placeholder="Enter Toy ID to search" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit" name="submit" class="btn btn-sm btn-outline-primary me-2">Search</button></td>
                </tr>
            </table>
        </form>
    </div>
    <hr>

    <?php
    if (isset($_POST['toyid'])) {
        $toyid = $_POST["toyid"];
        $search = "SELECT * FROM toys WHERE toyid='$toyid'";
        $res = mysqli_query($connecti, $search);

        if ($row = mysqli_fetch_assoc($res)) {
            //Display Toy Record Table
            echo '<div class="container"><h5>Following record found</h5><br><br>
            <form class="row" action="managetoys.php" method="post">
            <table  class="table table-bordered" align="center" width="50%" border="1">
            <tr>
                <th>Toy ID</th>
                <th>Toy Name</th>
                <th>Toy Price</th>
                <th>Toy Picture</th>
                <th>Actions</th>
            </tr>
            <tr> 
                <td><input type=hidden name="toyid" value="' . $row['toyid'] . '">' . $row['toyid'] . '</td>
                <td>' . $row['toyname'] . '</td>
                <td>Rs. ' . $row['toyprice'] . '</td>
                <td width="25%"><img src="../' . $row['toypicture'] . '" width="150" height="150"></td>
                <td><button type="submit" name="deletetoys" class="btn btn-danger me-2" required>Delete</button></td>
            </tr>
        </table>
        </form></div>';
        } else {
            echo '<br><div class="container"><h5>Entered record does not exist.</h5></div>';
        }
    } else {
        echo '<br><div class="container"><h5>Enter Toy ID to search.</h5></div>';
    }
    ?>
</div>
<?php require('../footer.php'); ?>