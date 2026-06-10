<?php require("header.php"); 
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
} ?>

<div class="col-lg-9 container">
    <div id="content">
    <br><br><h2>My Cart</h2>
        <div class="post" align="center">
            
            <div class="entry container col-lg-6 justify-content-center align-items-center" style="width: 80%; height: 20%" align="center">
                    <table width="100%" border="0">
                        <thead class="text-center">
                            <th> Toy Image</th>
                            <th>Toy Name</th>
                            <th width="120">Quantity</th>
                            <th>Toy Price</th>
                            <th>Total</th>
                            <th>Delete</th>
                        </thead>
                        <tr>
                            <td colspan="7">
                                <hr style="border:1px Solid #a1a1a1;">
                        </tr>
                        <?php
                        if (isset($_SESSION['cart1'])) {
                            foreach ($_SESSION['cart1'] as $id => $x) {
                                echo '<tbody class="text-center"><tr>
                                        <td><img src="' . $x['img'].'" alt="" width="70px" /></td>
                    					<td> ' . $x['nm'] . '</td>
                    					<td><form action="shoppingCart.php" method="POST"> 
                                        <input type="number" class="text-center form-control iquantity" name="modifyqty" id="modifyqty" onchange="this.form.submit();" value="'. $x['qty'] .'" min="1" max="5" required>
                                        <input type="hidden"  name="toyname" id="toyname"  value="'. $x['nm'] .'" >
                                        </form></td>
                    					<td> ' . $x['rate'] . '<input type="hidden" class="iprice"  value="'. $x['rate'] .'" ></td>
                    					<td class="itotal"> </td>
                    					<td> <form action="shoppingCart.php" method="POST">
                                        <button name="remove" class="form-control btn btn-sm btn-outline-danger" >Remove</button>
                                        <input type="hidden"  name="toyname" id="toyname"  value="'. $x['nm'] .'" >
                                        ';?><?php echo ' </form></td>
                    					</tr><tr>
                                        <td colspan="7">
                                            <hr style="border:1px Solid #a1a1a1;">
                                    </tr></tbody>';
                            }?>
                            
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="6" align="right">
                                <h3>Sub Total : Rs. </h3>
                            </td>
                            <td>
                                <h3 id="subtotal"> </h3>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <hr style="border:1px Solid #a1a1a1;">
                        </tr>
                        <Br>
                    </table>
                    <div class="container ">
                    <form action="checkout.php" method="POST"><a href="checkout.php">
                    <button  type="submit" class="btn btn-lg btn-outline-primary" >Proceed to checkout<br></button>
                    <input type="hidden"  id="subtotal1" name="subtotal" onchange="this.form.submit();" ></a></form>
                    <br><a href='home.php'>Continue Shopping</a>
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
var iprice=document.getElementsByClassName('iprice');
var iquantity = document.getElementsByClassName('iquantity');
var itotal=document.getElementsByClassName('itotal');
var subtotal= document.getElementById('subtotal');
var subtotal1= document.getElementById('subtotal1');
var stot=0;
function subTotal(){
    stot=0;
    for(i=0;i<iprice.length;i++){
        itotal[i].innerText=(iprice[i].value)*(iquantity[i].value);
        stot= stot+ (iprice[i].value)*(iquantity[i].value);
    }
    subtotal.innerText=stot; 
    subtotal1.innerText=stot;
    subtotal1.innerHTML=stot;  
    subtotal1.value=stot;
}
subTotal();
</script>
<?php require('footer.php'); ?>