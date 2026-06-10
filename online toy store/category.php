<?php require('header.php');
include "connectToMysql.php";
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
}
$cat = $_GET['cat'];
$catsql = "SELECT toys.toyname,toys.toyprice,toys.toypicture,toys.toyid ,category.catname FROM toys LEFT JOIN category ON category.catid=toys.catid WHERE toys.catid='$cat'";

?>
<div class="product-section content">
    <div class="container col-lg-12">

        <?php if ($res = mysqli_query($connecti, $catsql)) {
            if ($result = mysqli_fetch_assoc($res)) {

                $page_head = $result['catname'];
            }
        ?>

            <div class="row">
                <div class="col-lg-12">
                    <br><br>
                    <h2 class="section-head"><?php echo $page_head; ?></h2><br><br>
                </div>
            </div>
            <div class="row">
                    <?php
                    foreach ($res as $row) { ?>
                        <div class="col-4 col-sm-4">
                            <div class="product-grid">
                                <div class="product-image" align="center">
                                    <a class="image" href="detail.php?name=<?php echo $row['toyname']; ?> ">
                                        <img class="pic-1" src="<?php echo $row['toypicture']; ?>" width="200" height="200">
                                    </a>
                                    <div class="product-button-group" >
                                        <?php echo '<h4><br>' .$row['toyname']. '<br> Rs. ' .$row['toyprice']. '</h4>
                                        <form action="shoppingCart.php" method="post">
                                            <input type="hidden" id="quantity" name="quantity" value="1" />
                                            <input type="hidden" id="toyid" name="toyid" value="' . $row["toyid"] . '"/>
                                            <input type="hidden" id="toypicture" name="toypicture" value="' . $row["toypicture"] . '"/>
						                    <input type="hidden" id="toyname" name="toyname" value="' . $row["toyname"] . '"/>
						                    <input type="hidden" id="toyprice" name="toyprice" value="' . $row["toyprice"] . '"/>
                                            <button id="add-to-cart" name="add-to-cart" class=" btn btn-lg btn-outline-primary" >Add to cart</button>
						                </form><br><br>'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php    }
                } else { ?>
                        <div class="empty-result">Result Empty</div>
                    <?php } ?>
                </div>
            </div>
    </div>
</div>






<?php require('footer.php'); ?>