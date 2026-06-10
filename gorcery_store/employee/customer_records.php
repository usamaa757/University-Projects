<?php
include('header.php');
include('../db_connection.php');

// Fetch all customers from the database
$query = "SELECT * FROM customers";
$result = mysqli_query($conn, $query);
?>


<div class="container mt-5 rounded shadow border p-0">
    <h3 class="bg-dark text-center text-white p-2">View Customers</h3>
    <div class="p-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Subscription Status</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through and display each customer
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['subscription_status']}</td>
                            <td>{$row['date_added']}</td>
                            <td><a href='send_discount.php?customer_id={$row["customer_id"]}'>Discount</a></td>
                           
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    </body>

    </html>