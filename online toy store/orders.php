<?php require('header.php');
include "connectToMysql.php";
if (($_SESSION['isUser']) == NULL) {
    header('location:login.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container content-wrapper">
    <div class="container">
        <br>
        <h1>My Orders</h1><br><br>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-light">
                    <thead>
                        <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Address</th>
                            <th scope="col">Pay Mode</th>
                            <th scope="col">Orders</th>
                            <th scope="col">Pay Status</th>
                            <th scope="col">Order Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $user = $_SESSION['userid'];
                        $sql = "SELECT orders.order_id, orders.orderdate,orders.orderstatus ,orders.shipinfo, orders.totalamt,orders.paymode,users.username,users.contact, payments.paystatus FROM orders  LEFT JOIN cart ON cart.order_id=orders.order_id LEFT JOIN users ON users.user_id=orders.user_id LEFT JOIN payments ON payments.order_id=orders.order_id WHERE users.user_id='$user' GROUP BY orders.order_id ORDER BY orders.order_id ";
                        $result = mysqli_query($connecti, $sql);
                        while ($res = mysqli_fetch_assoc($result)) {
                            echo '
                        <tr>
                            <td>' . $res["order_id"] . '</td>
                            <td>' . $res["username"] . '</td>
                            <td>' . $res["contact"] . '</td>
                            <td>' . $res["shipinfo"] . '</td>
                            <td>' . $res["paymode"] . '</td>
                            <td>
                            <table class="table table-bordered table-light">
                            <thead>
                                <tr>
                                    <th scope="col">Toy Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $order = $res["order_id"];
                            $orderquery = "SELECT * FROM cart WHERE cart.order_id='$order'";
                            $orderresult = mysqli_query($connecti, $orderquery);
                            while ($orderres = mysqli_fetch_assoc($orderresult)) {
                                echo '
                                <tr>
                                    <td>' . $orderres["toyname"] . '</td>
                                    <td>' . $orderres["price"] . '</td>
                                    <td>' . $orderres["quantity"] . '</td>
                                </tr>';
                            }
                            echo '
                            </tbody>
                            </table>
                            </td>
                            <td>';
                            if ($res['paystatus'] == 'paid') {
                                echo ' <span class="badge bg-success">Paid</span>';
                            } else {
                                echo ' <span class="badge bg-danger">Verification In Process</span>';
                            }
                            echo '
                            </td>
                            <td>';
                            if ($res['orderstatus'] == 'Pending') {
                                echo ' <span class="badge bg-danger">Pending</span>';
                            } else {
                                echo ' <span class="badge bg-success">Shipped</span>';
                            }
                            echo '
                            </td>
                        </tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require('footer.php'); ?>