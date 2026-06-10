<?php require('../header.php');
require('../connectToMysql.php');
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
} ?>
<div class="container">
    <div class="row">
        <h2><br>Reports Management</h2>
    </div>
    <hr><br><br>
    <div class="row">
        <div class="col-lg-9">
            <form class="" action="" method="post">
                <table width="80%">
                    <tr>
                        <th height="50">From Date :</th>
                        <td>
                            <input type="date" name="fdate" class="form-control" id="fdate" required>
                        </td>
                    </tr>
                    <tr>
                        <th height="50">To Date :</th>
                        <td>
                            <input type="date" name="tdate" class="form-control" id="tdate" required>
                        </td>
                    </tr>
                    <tr>
                        <th height="50" scope="row">Request Type :</th>
                        <td>
                            <div class="col-auto"><input type="radio" name="requesttype" value="profit" checked>Profit Report</div>
                            <div class="col-auto"><input type="radio" name="requesttype" value="expense">Expense Report</div>
                        </td>
                    </tr>
                    <tr>
                        <th height="50"></th>
                        <td>
                            <button class="btn-primary btn" type="submit" name="submit">Generate</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-9 "><br><br>
            <?php
            if (isset($_POST['submit'])) {
                $fdate = $_POST['fdate'];
                $tdate = $_POST['tdate'];

                $rtype = $_POST['requesttype'];
                $month1 = strtotime($fdate);
                $month2 = strtotime($tdate);
                $m1 = date("F", $month1);
                $m2 = date("F", $month2);
                $y1 = date("Y", $month1);
                $y2 = date("Y", $month2);
            ?>
                <h2 align="center" style="color:blue"><?php if ($rtype == 'profit') { echo "Profit"; } else {echo "Expense";} ?> Report ( <?php echo $m1 . "-" . $y1; ?> to <?php echo $m2 . "-" . $y2; ?>)</h2>
                <hr>
                <div class="row">
                    <table class="table table-bordered table-light" width="100%">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Month / Year </th>
                                <th>Sales</th>
                                <th>Expense</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql1 = "SELECT * FROM orders";
                            if (mysqli_query($connecti, $sql1)) {
                                $sql = "SELECT month(orders.orderdate) as lmonth,year(orders.orderdate) as lyear,orders.order_id from orders where date(orders.orderdate) between '$fdate' and '$tdate' group by lmonth,lyear order by order_id";
                                $ret = mysqli_query($connecti, $sql);
                                if ($ret) {
                                    $cnt = 1;
                                    $sales = 0;
                                    $expense = 0;
                                    while ($row = mysqli_fetch_assoc($ret)) {
                            ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row['lmonth'] . "/" . $row['lyear']; ?></td>
                                            <?php $sql2 = "SELECT * FROM cart";
                                            $res2 = mysqli_query($connecti, $sql2);
                                            while (mysqli_fetch_array($res2)) {
                                                $stot = 0;
                                                $etot = 0;
                                                $order = $row["order_id"];
                                                $orderquery = "SELECT cart.price, cart.quantity,toys.costprice FROM cart left join toys on toys.toyname=cart.toyname WHERE cart.order_id='$order'";
                                                $orderresult = mysqli_query($connecti, $orderquery);
                                                while ($orderres = mysqli_fetch_array($orderresult)) {

                                            ?>
                                                    <?php $stotal =  $orderres["price"] * $orderres["quantity"]; ?>
                                                    <?php $etotal = $orderres['costprice'] * $orderres['quantity']; ?>
                                            <?php
                                                    $stot += $stotal;
                                                    $etot += $etotal;
                                                }
                                            } ?>
                                            <td><?php echo $stot; ?></td>
                                            <td><?php echo $etot; ?></td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                        $sales += $stot;
                                        $expense += $etot;
                                    }
                                    if ($rtype == 'profit') { ?>
                                        <tr>
                                            <td colspan="2" align="center" style="background-color: #88D6FF">
                                                <h3>Total Profit</h3>
                                            </td>
                                            <td colspan="2" style="background-color: #88D6FF">
                                                <h3><?php echo ($sales - $expense); ?></h3>
                                            </td>
                                        </tr>
                            <?php } else { ?>
                                        <tr>
                                            <td colspan="2" align="center" style="background-color: #88D6FF">
                                                <h3>Total Expense</h3>
                                            </td>
                                            <td colspan="2" style="background-color: #88D6FF">
                                                <h3><?php echo $expense; ?></h3>
                                            </td>
                                        </tr>
                            <?php 
                                    }
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            <?php
            } ?>
        </div>
    </div>
</div>
<?php require('../footer.php'); ?>