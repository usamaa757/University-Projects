<?php require('../header.php');
require('../connectToMysql.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container col-lg-9 justify-content-center align-items-center">
    <div>
        <br><br>
        <h2>Update Toys</h2>
    </div>
    <hr>
    <div class="col-lg-6">
        <form method="post" action="" class="d-grid gap-1">
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

        if ($row = mysqli_fetch_assoc($res)) { ?>
            <br><br>
            <div class="justify-content-center align-items-center">
                <h4>Following record found</h4><br>
                <form class="d-grid gap-2" method="post" action="managetoys.php" enctype="multipart/form-data">
                    <div class="col-lg-12 form-group d-grid gap-2">
                        <table>
                            <tr>
                                <th>Toy ID:</th>
                                <td><input type="text" name="toyid" class="form-control" value="<?php echo $row['toyid']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>Toy Name:</th>
                                <td><input type="text" name="name" class="form-control" value="<?php echo $row['toyname']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>Toy Price:</th>
                                <td><input type="text" name="price" class="form-control" value="<?php echo $row['toyprice']; ?>"></td>
                            </tr>
                            <tr>
                                <th>Toy Category:</th>
                                <td><select class="form-select" name="category" required>
                                        <option selected value="<?php echo $row['catid']; ?>">
                                            <?php $id = $row['catid'];
                                            $sql = "SELECT * FROM category WHERE catid='$id'";
                                            $res = mysqli_query($connecti, $sql);
                                            if ($catname = mysqli_fetch_assoc($res)) {
                                                echo $catname['catname'];
                                            } ?></option>
                                        <?php
                                        $query = 'SELECT * FROM category';
                                        $result = mysqli_query($connecti, $query);
                                        foreach ($result as $cat) {
                                        ?>
                                        <option value="<?php echo $cat['catid']; ?>"><?php echo $cat['catname']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select></td>
                            </tr>
                            <tr>
                                <th>Toy Image:</th>
                                <td><input type="file" name="fileToUpload" id="fileToUpload" class="form-control"></td>
                                <td><img id="image" src="<?php echo $row['toypicture']; ?>" alt="" width="100px" /></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" class="btn btn-outline-primary me-2" name="updatetoys">Update Toy</button></td>
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
        echo '<div class="container"><br><h5>Enter Toy ID to search records.</h5></div>';
    }
    ?>
</div>
<?php require('../footer.php'); ?>