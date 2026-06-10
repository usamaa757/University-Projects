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
    <div class="card mb-4 bg-blue text-white">
        <div class="card-body">
            <h4>Your Profile</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['user_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Plant Preferences:</strong> <?php echo htmlspecialchars($user['preferences']); ?></p>
        </div>
    </div>

    <!-- Order History -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Your Prefereances</h4>
            <table class="table">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Plant ID</th>
                        <th>Plant Name</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch plants listed by the seller
                    $plants_query = "SELECT * FROM plants WHERE plant_type = '{$user['preferences']}'";
                    $plants_result = mysqli_query($conn, $plants_query);

                    if (mysqli_num_rows($plants_result) > 0) {
                        while ($plant = mysqli_fetch_assoc($plants_result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($plant['plant_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($plant['plant_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($plant['plant_type']) . "</td>";
                            echo "<td>Rs " . htmlspecialchars($plant['price']) . "</td>";
                            echo "<td>" . htmlspecialchars($plant['quantity']) . "</td>";
                            echo "<td>
                                    <a href='plants_list.php' class='btn btn-success btn-sm'>Purchase</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No plants listed by prefereances yet.</td></tr>";
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