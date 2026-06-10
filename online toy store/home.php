<?php require('header.php');
require('connectToMysql.php');
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container col-lg-12">
    <div class="row">
        <div class="col-lg-12">
            <br><br>
            <h2 class="section-head">Shop Latest Toys</h2><br><br>
        </div>
    </div>
    <div class="row">
        <?php
        $sql = "SELECT * FROM toys";
        $res_data = mysqli_query($connecti, $sql);
        while ($row = mysqli_fetch_array($res_data)) {
            if($row['quantity']!=0){
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
        }}
        ?>
    </div>
</div>
<br>
<?php require('footer.php'); ?>