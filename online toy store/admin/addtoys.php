<?php require('../header.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>

<div class="container col-lg-9 justify-content-center align-items-center">
    <div><br><br>
        <h2>Add Toys</h2>
    </div>
    <hr><br>
    <div class="col-lg-8" align="center">
        <form action="managetoys.php" method="post" enctype="multipart/form-data" class="d-grid gap-2">
            <div class="form-group ">
                <table>
                    <tr>
                        <th>Toy ID:</th>
                        <td><input type="text" name="toyid" class="form-control" required></td>
                    </tr>
                    <tr>
                        <th>Toy Name:</th>
                        <td><input type="text" name="name" class="form-control" required></td>
                    </tr>
                    <tr>
                        <th>Toy Price:</th>
                        <td><input type="number" name="price" class="form-control" required></td>
                    </tr>
                    <tr>
                        <th>Toy Category:</th>
                        <td><select class="form-select" name="category" required>
                                <option selected>Select Category</option>
                                <?php
                                require('../connectToMysql.php');
                                $query ='SELECT * FROM category';
                                $result = mysqli_query($connecti,$query);
                                foreach($result as $cat){
                                    ?>
                                    <option value="<?php echo $cat['catid'];?>"><?php echo $cat['catname'];?></option>
                                    <?php
                                }
                                ?>
                            </select></td>
                    </tr>
                    <tr>
                        <th>Toy Picture:</th>
                        <td><input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button type="submit" name="addtoys" class="btn btn-outline-primary me-2">Add Toy</button></td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>
<?php require('../footer.php'); ?>