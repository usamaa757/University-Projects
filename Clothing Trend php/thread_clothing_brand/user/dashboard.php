<?php
include 'header.php';

// Include database connection
include '../db_connection.php';

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);


?>

<div class="container mt-5 round border shadow p-3">

    <!-- User Details -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Your Profile</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['user_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </div>

    <!-- Order History -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Previous Orders</h4>
            <table class="table">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch cloths listed in orders by the seller
                    $cloths_query = "
                    SELECT 
                        o.order_id, 
                        c.cloth_id, 
                        c.size, 
                        c.price, 
                        o.quantity, 
                        o.total_price, 
                        c.category_id, 
                        cat.category_name 
                    FROM 
                        orders o 
                    JOIN 
                        cloths c 
                    ON 
                        o.cloth_id = c.cloth_id 
                    JOIN 
                        categories cat 
                    ON 
                        c.category_id = cat.category_id 
                    WHERE 
                        o.user_id = '{$user['user_id']}' LIMIT 3
                    ";
                    $cloths_result = mysqli_query($conn, $cloths_query);


                    if (mysqli_num_rows($cloths_result) > 0) {
                        while ($cloth = mysqli_fetch_assoc($cloths_result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($cloth['category_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($cloth['size']) . "</td>";
                            echo "<td>" . htmlspecialchars($cloth['quantity']) . "</td>";
                            echo "<td>Rs " . htmlspecialchars($cloth['price']) . "</td>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No order yet.</td></tr>";
                    }
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>

</html>