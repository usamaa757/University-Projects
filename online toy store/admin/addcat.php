<?php require('../header.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>

<div class="container col-lg-9 justify-content-center align-items-center">
    <div><br><br>
        <h2>Add Category</h2>
    </div>
    <hr><br>
    <div class="col-lg-8" align="center">
        <form action="managecat.php" method="post" enctype="multipart/form-data" class="d-grid gap-2">
            <div class="">
            <table>
                <tr>
                    <th>Category ID:</th>
                    <td><input type="number" name="catid" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Category Title:</th>
                    <td><input type="text" name="catname" class="form-control" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit" name="addcat" class="btn btn-md btn-outline-primary ">Add Category</button></td>
                </tr>
            </table>
            </div>
        </form>
    </div>
</div>
<?php require('../footer.php'); ?>