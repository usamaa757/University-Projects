<?php require('header.php');
include "connectToMysql.php";

$search = $_POST["toySearch"];
$sql = "SELECT * FROM toys, category WHERE toys.catid=category.catid and (toys.toyname LIKE '%$search%' or category.catname LIKE '%$search%')";
$result = mysqli_query($connecti, $sql);
?>
<div class="container col-lg-12">
    <div class="row">
        <div class="col-lg-9">
            <br><br>
            <h2 class="section-head">Search Results</h2><br><br>
        </div>
    </div>
    <?php
    if (($_SESSION['isUser']) == NULL) {
        header('location:login.php'); // redirect to login page if user details is not set in sessions
    } else { ?>
        <div class='row'>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                echo '<div class="col-4 col-sm-4"><br>
        <div class="product-image" align="center">
            <a href="detail.php?name=' . $row['toyname'] . '"><img src="../' . $row['toypicture'] . '"alt="productImage" width="200" height="200"/></a>
              <br><h4>' . $row['toyname'] . '<br> Rs. ' . $row['toyprice'] . '</h4>
                <form action="shoppingCart.php" method="post">
                    <input type="hidden" id="quantity" name="quantity" value="1"/>
                    <input type="hidden" id="toyid" name="toyid" value="' . $row["toyid"] . '"/>
                    <input type="hidden" id="toypicture" name="toypicture" value="' . $row["toypicture"] . '"/>
                    <input type="hidden" id="toyname" name="toyname" value="' . $row["toyname"] . '"/>
                    <input type="hidden" id="toyprice" name="toyprice" value="' . $row["toyprice"] . '"/>
                    <button id="add-to-cart" name="add-to-cart" class=" btn btn-lg btn-outline-primary" >Add to cart</button>
                </form><br><br>
             </div></div>';
            }
        } else {
            echo '<div class="col-lg-9">
            <br><br>
            <h4>No Results Found</h2>
        </div>';
        }
        echo '</div>';
    } ?>
</div>
<?php require('footer.php'); ?>