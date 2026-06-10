<?php include '../db.php'; ?>
<?php include 'header.php'; ?>

<div class="container shadow rounded border mt-5">
    <h3 class="text-center mb-4">Car Inventory</h3>


    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM cars");

                if ($result->num_rows > 0) {
                    // Fetch and display car data
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
            <td>{$row['brand']}</td>
            <td>{$row['model']}</td>
            <td>Rs. {$row['price']}</td>
            <td>{$row['status']}</td>
            <td>
                <a href='edit_car.php?car_id={$row['car_id']}' class='btn btn-sm'>Edit</a> 
                <a href='delete_car.php?car_id={$row['car_id']}' onclick='return confirm(\"Are you sure?\")' class='btn btn-sm'>Delete</a>
            </td>
        </tr>";
                    }
                } else {
                    // Display message if no cars are found
                    echo "<tr><td colspan='6' class='text-center'>No cars found.</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>
</div>
</body>

</html>