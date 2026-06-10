<?php require('../header.php');
require('../connectToMysql.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container col-lg-9 justify-content-center align-items-center">
    <div>
    <br><br><h2>Delete Category</h2>
    </div>

    <hr>
    <div class="col-lg-6">
    <form method="post" action="" class="d-grid gap-2">
        <table>
            <tr>
                <th>Category ID:</th>
                <td><input type="number" name="catid" class="form-control" placeholder="Enter Category ID to search" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="submit" class="btn btn-sm btn-outline-primary me-2">Search</button></td>
            </tr>
        </table>
    </form>
    </div><hr>

    <?php
    if (isset($_POST['catid'])) {
        $catid = $_POST["catid"];
        $search = "SELECT * FROM category WHERE catid='$catid'";
        $res = mysqli_query($connecti, $search);

        if ($row = mysqli_fetch_assoc($res)) {
            //Display Category Record Table
            echo '<div class="container"><h4>Following record found</h4><br><br>
            <form class="row" action="managecat.php" method="post">
            <table  class="table table-bordered" align="center" width="50%" border="1">
            <tr>
                <th>Category ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
            <tr> 
                <td><input type=hidden name="catid" value="' . $row['catid'] . '">' . $row['catid'] . '</td>
                <td>' . $row['catname'] . '</td>
                <td><button type="submit" name="deletecat" class="btn btn-danger me-2" required>Delete</button></td>
            </tr>
             </table>
             </form></div>';
        } else {
            echo '<br><div class="container"><h5>Entered record does not exist.</h5></div>';
        }
    } else {
        echo '<br><div class="container"><h5>Enter Category ID to search.</h5></div>';
    }
    ?>
</div>
<?php require('../footer.php'); ?>