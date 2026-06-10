<?php require('../header.php');
require('../connectToMysql.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container">
    <div class="row">
        <h2><br>Stock Management</h2>
    </div>
    <hr><br><br>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-light table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Toy ID</th>
                        <th scope="col">Toy Name</th>
                        <th scope="col">Toy Image</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Cost Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM toys ORDER BY toyid";
                    $result1 = mysqli_query($connecti, $sql);
                    while ($res = mysqli_fetch_assoc($result1)) {
                        echo '
                    <tr>
                        <td>' . $res["toyid"] . '</td>
                        <td>' . $res["toyname"] . '</td>
                        <td><img src="../' . $res['toypicture'] . '" width="70px" height="70px" /></td>
                        <td><form action="" method="POST" class="row"><div class="col-auto">
                            <input type="number" class="form-control" name="modifyqty" value="' . $res['quantity'] . '" required></input>
                            <input type="hidden"  name="toyid" value="' . $res['toyid'] . '" ></input></div>
                            <div class="col-auto"><button class="btn btn-outline-danger" type="submit" name="updatestock" onclick="this.form.submit();">Update Stock</button></div></form></td>
                        <td><form action="" method="POST" class="row"><div class="col-auto">
                            <input type="number" class="form-control" name="costprice" value="' . $res['costprice'] . '" required></input>
                            <input type="hidden"  name="toyid" value="' . $res['toyid'] . '" ></input></div>
                            <div class="col-auto"><button class="btn btn-outline-danger " type="submit" name="updateprice" onclick="this.form.submit();">Update Price</button></div></form></td>
                    </tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
if (isset($_POST['updatestock'])) {
    $toyid = $_POST['toyid'];
    $qty = $_POST['modifyqty'];
    $query = "UPDATE toys SET quantity='$qty' where toyid = '$toyid'";
    if (mysqli_query($connecti, $query)) {
        echo '<script>alert("Record updated successfully!"); window.location="stock.php"; </script>';
    } else {
        echo '<script>alert("Failed to update record!"); window.location="stock.php"; </script>';
    }
    exit();
}
if (isset($_POST['updateprice'])) {
    $toyid = $_POST['toyid'];
    $price = $_POST['costprice'];
    $query1 = "UPDATE toys SET costprice='$price' where toyid = '$toyid'";
    if (mysqli_query($connecti, $query1)) {
        echo '<script>alert("Record updated successfully!"); window.location="stock.php"; </script>';
    } else {
        echo '<script>alert("Failed to update record!"); window.location="stock.php"; </script>';
    }
    exit();
}
?>
<?php require('../footer.php'); ?>