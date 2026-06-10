<?php require('../header.php');
require('../connectToMysql.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container col-lg-9 justify-content-center align-items-center">
    <div>
    <br><br><h2>Update Category</h2>
    </div>
    <hr>
    <div class="col-lg-6">
        <form method="post" action="" class="d-grid gap-1">
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
    </div>
    <hr>
    <?php
    if (isset($_POST['catid'])) {
        $catid = $_POST["catid"];
        $search = "SELECT * FROM category WHERE catid='$catid'";
        $res = mysqli_query($connecti, $search);

        if ($row = mysqli_fetch_assoc($res)) { ?>
            <br><br>
            <div class=" justify-content-center align-items-center">
                <h4>Following record found</h4><br>
                <form class="d-grid gap-2" method="post" action="managecat.php" enctype="multipart/form-data">
                    <div class="col-lg-6 form-group d-grid gap-2">
                        <table>
                            <tr>
                                <th>Category ID:</th>
                                <td><input type="number" name="catid" class="form-control" value="<?php echo $row['catid']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>Category Name:</th>
                                <td><input type="text" name="catname" class="form-control" value="<?php echo $row['catname']; ?>" /></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" class="btn btn-outline-primary me-2" name="updatecat">Update Category</button></td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
    <?php
        } else {
            echo '<div class="container"><br><h5>Entered record does not exist.</h5></div>';
        }
    } else {
        echo '<div class="container"><br><h5>Enter Category ID to search records.</h5></div>';
    }
    ?>

    <?php require('../footer.php'); ?>