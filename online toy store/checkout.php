<?php require('header.php');
include "connectToMysql.php";
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
}
if (isset($_POST['subtotal'])) {

    $_SESSION['subtotal'] = $_POST['subtotal'];
}
if (isset($_SESSION['cart1'])) {
    $size = sizeof($_SESSION['cart1']);
    if ($size == 0) {
        echo "<script>alert('No items in cart!');</script>";
        header("refresh:0;url=cart.php");
    } else {
?><div class="container ">
            <br><br>
            <h2>Shipping Details</h2>
            <hr>
            <div class="row">
                <div class="col-lg-9 col-9 justify-content-center">
                    <div>Please fill in your shipping details to confirm your order!</div>
                    <hr>
                    <?php
                    $userid = $_SESSION['userid'];
                    $search = "SELECT * FROM users WHERE user_id='$userid'";
                    $res = mysqli_query($connecti, $search);
                    if ($row = mysqli_fetch_assoc($res)) {
                    ?><br>
                        <div class=" justify-content-center align-items-center">
                            <form method="post" action="" enctype="multipart/form-data">
                                <div class=" form-group d-grid gap-2">
                                    <table>
                                        <tr>
                                            <td><input type="hidden" name="user_id" class="form-control" value="<?php echo $row['user_id']; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <th>Full Name:</th>
                                            <td><input type="text" name="username" class="form-control" disabled value="<?php echo $row['username']; ?>" /></td>
                                            <th>Email:</th>
                                            <td><input type="text" name="email" class="form-control" disabled value="<?php echo $row['email']; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <th>Contact:</th>
                                            <td><input type="number" name="contact" class="form-control" disabled value="<?php echo $row['contact']; ?>" /></td>
                                            <th>City</th>
                                            <td><select name="city" class="form-control" required>
                                                    <option value="" disabled selected>Select City</option>
                                                    <option value="Islamabad">Islamabad</option>
                                                    <option value="Karachi" id="Sindh">Karachi</option>
                                                    <option value="Lahore" id="Punjab">Lahore</option>
                                                    <option value="Peshawar" id="KPK">Peshawar</option>
                                                    <option value="Quetta" id="Balochistan">Quetta</option>
                                                </select></td>
                                        </tr>
                                        <tr>
                                            <th>Street Address:</th>
                                            <td><input type="textarea" name="stadd" class="form-control" required placeholder="e.g. House No.6, Link Road." /></td>
                                            <th>State:</th>
                                            <td><select name="state" class="form-control" required>
                                                    <option value="" disabled selected>Select State or Province</option>
                                                    <option value="Balochistan">Balochistan</option>
                                                    <option value="KPK">KPK</option>
                                                    <option value="Punjab">Punjab</option>
                                                    <option value="Sindh">Sindh</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Payment Method:</th>
                                            <td>
                                                <div class="col-auto"><input type="radio" name="paymode" value="Online" checked>Online Payment</div>
                                                <div class="col-auto"><input type="radio" name="paymode" value="Voucher">Voucher Payment</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div align="right"><br><input type="submit" class="btn btn-lg btn-outline-primary me-2" name="confirmorder" value="Confirm Order">
                                    <br><br><a href='home.php'>Continue Shopping </a>
                                </div>
                            </form>
                        </div>
                    <?php
                    } else {
                        echo "<br><br><h5>Error!</h5>";
                    }
                    ?>
                </div>

                <div class="col-3 col-md-3 text-bg-light ">
                    <br>
                    <h4>Order Summary</h4>
                    <hr>
                    <div class="container col-lg-6 justify-content-center align-items-center" style="width: 100%; height: 20%" align="center">
                        <form method="post" action="" enctype="multipart/form-data">
                            <table width="100%" border="0">
                                <thead class="text-center">
                                    <th> Toy</th>
                                    <th>Name</th>
                                    <th width="120">Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </thead>
                                <tr>
                                    <td colspan="7">
                                        <hr style="border:1px Solid #a1a1a1;">
                                </tr>
                                <?php
                                if (isset($_SESSION['cart1'])) {

                                    foreach ($_SESSION['cart1'] as $id => $x) {
                                        echo '<tbody class="text-center" >
                                    <tr>
                                        <td><img src="' . $x['img'] . '" alt="" width="50px" /></td>
                    					<td> ' . $x['nm'] . '</td>
                    					<td> ' . $x['qty'] . '</td>
                    					<td> ' . $x['rate'] . '</td>
                    					<td>' . $x['qty'] * $x['rate'] . ' </td>
                    				</tr>
                                    <tr><td colspan="7">
                                            <hr style="border:1px Solid #a1a1a1;">
                                    </tr></tbody>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td colspan="6" align="right">
                                        <h5>Sub Total : Rs. <?php echo $_SESSION["subtotal"]; ?></h5>
                                    </td>
                                    <td>
                                        <h5> </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <hr style="border:1px Solid #a1a1a1;">
                                </tr>
                                <Br>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
    <?php
        if (isset($_POST['confirmorder'])) {
            $userid = $_SESSION["userid"];
            $paymode = $_POST['paymode'];
            $stadd = $_POST['stadd'];
            $city = $_POST['city'];
            $state = $_POST['state'];
            $date = date('Y-m-d');
            $address = $stadd . ", " . $city . ", " . $state . ".";
            $stot = $_SESSION["subtotal"];

            $sql = "INSERT INTO orders( user_id, shipinfo, paymode, orderdate, totalamt, orderstatus) VALUES ('$userid','$address','$paymode', '$date', '$stot','Pending')";

            if (mysqli_query($connecti, $sql)) {
                $order_id = mysqli_insert_id($connecti);
                $sql1 = "INSERT INTO cart( order_id, toyname, quantity, price) VALUES (?,?,?,?)";
                $stmt = mysqli_prepare($connecti, $sql1);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "isii", $order_id, $toyname, $quantity, $price);
                    foreach ($_SESSION['cart1'] as $key => $value) {
                        $toyname = $value['nm'];
                        $quantity = $value['qty'];
                        $price = $value['rate'];
                        mysqli_stmt_execute($stmt);
                    }
                    foreach ($_SESSION['cart1'] as $id => $x) {
                        $username = $_SESSION["name"];
                        $itemid = $x['id'];
                        $itemname = $x['nm'];
                        $price = $x['rate'];

                        $s = "SELECT quantity FROM toys WHERE toyid='" . $itemid . "' ";
                        $res = mysqli_query($connecti, $s);
                        $row = mysqli_fetch_array($res);
                        $quant = $row[0] - $x['qty'];
                        $updt = "UPDATE toys SET quantity='" . $quant . "' WHERE toyid='" . $itemid . "'";
                        mysqli_query($connecti, $updt);
                        unset($_SESSION['cart1']);
                    }
                    echo "<script type='text/javascript'>alert('Your Order has been Confirmed!'); window.location.replace('home.php'); </script>";
                } else {
                    echo "<script type='text/javascript'>alert('Sql query prepare error!');</script>";
                }
            } else {
                echo "<script type='text/javascript'>alert('Sql error!');</script>";
            }
        }
    }
} else {
    echo "<script>alert('No items in cart');</script>";
    header("refresh:0;url=cart.php");
}
    ?>
    <?php require('footer.php'); ?>