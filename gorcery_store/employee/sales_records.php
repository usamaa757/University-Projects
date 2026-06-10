<?php
include('header.php');
include('../db_connection.php');

// Fetch sales records for today
$sale_date = date('Y-m-d'); // Current date
$query = "SELECT s.sale_id, p.product_name, s.quantity, s.total_amount, s.sale_date 
          FROM sales s 
          JOIN products p ON s.product_id = p.product_id 
          WHERE s.sale_date = '$sale_date'";
$result = mysqli_query($conn, $query);

// Variables to store totals
$total_quantity = 0;
$total_amount = 0;
?>

<!DOCTYPE html>
<html lang="en">

<div class="container mt-5 rounded shadow border p-0">
    <h3 class="bg-dark text-center text-white p-2">Sales Records</h3>
    <div class="p-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Amount</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through and display each sale
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['product_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>\${$row['total_amount']}</td>
                            <td>{$row['sale_date']}</td>
                          </tr>";

                    // Add to totals
                    $total_quantity += $row['quantity'];
                    $total_amount += $row['total_amount'];
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="1">Totals</th>
                    <th><?php echo $total_quantity; ?></th>
                    <th>$<?php echo number_format($total_amount, 2); ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</body>

</html>