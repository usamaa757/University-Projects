<?php 
include 'header.php';
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
} 
include "connectToMysql.php";
$toyname = $_GET['name'];
$sql = "SELECT * FROM toys WHERE toyname='$toyname'";
$result = mysqli_query($connecti, $sql);
$row = mysqli_fetch_assoc($result);

?>
<div class="row">
        <?php foreach($result as $row){ ?>
			<div class="row"><br><br></div>
                <div class="col-md-2"></div>
                <div class="col-md-2">
                    <div class="product-image">
                        <img id="product-img"  width="150" height="150" src="../<?php echo $row['toypicture']; ?>" alt=""/>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-3">
                        <h2 class="title" id="nameId"><?php echo $row['toyname']; ?></h2><hr><br>
						<h5 class="id" id="toyIdd">Item ID: <?php echo $row['toyid']; ?></h5><br>
                        <h4 class="price" id="priceId">Rs. <?php echo $row['toyprice']; ?></h4><br>
						<form class="d-grid gap-2" action="shoppingCart.php" method="post">
						<?php echo '
						<table>
            			<tr>
                		<td><h5>Quantity : </h5></td>
						<td><input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1"  max="5"/></td>
						</tr>
						</table>
						<input type="hidden" id="toyid" name="toyid" value="' . $row["toyid"] . '"/>
						<input type="hidden" id="toypicture" name="toypicture" value="' . $row["toypicture"] . '"/>
						<input type="hidden" id="toyname" name="toyname" value="' . $row["toyname"] . '"/>
						<input type="hidden" id="toyprice" name="toyprice" value="' . $row["toyprice"] . '"/>' ; ?>
                        <button id="add-to-cart" name="add-to-cart" class="form-control btn btn-lg btn-outline-primary" >Add to cart</button>
						</form>
                </div>
                <div class="col-md-2"></div>
    <?php   } ?>

</div>

<?php require('footer.php'); ?>