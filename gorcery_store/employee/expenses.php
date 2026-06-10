<?php
include('header.php');
include('../db_connection.php');

// Fetch all expenses from the database
$query = "SELECT * FROM expenses";
$result = mysqli_query($conn, $query);

// Initialize total amount
$total_amount = 0;
?>

<div class="container mt-5 rounded shadow border p-0">
    <h3 class="bg-dark text-center text-white p-2">Store Expenses Record</h3>
    <div class="p-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Expense Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Expense Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through and display each expense
                while ($row = mysqli_fetch_assoc($result)) {
                    // Add current amount to the total
                    $total_amount += $row['amount'];

                    echo "<tr>
                            <td>{$row['expense_type']}</td>
                            <td>Rs. {$row['amount']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['expense_date']}</td>
                          </tr>";
                }
                ?>
                <!-- Display total at the bottom -->
                <tr>
                    <td colspan="3" class="text-right font-weight-bold">Total Expenses</td>
                    <td class="font-weight-bold">Rs. <?php echo number_format($total_amount, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>