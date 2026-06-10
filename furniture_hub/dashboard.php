<?php
include("config.php");
include("navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // redirect if not logged in
    exit();
}

// Fetch logged-in user details
$user_id = $_SESSION['user_id'];
$query = "SELECT name, email, role FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$name = $user['name'];
$email = $user['email'];
$role = $user['role'];

// Fetch counts
$total_users_result = mysqli_query($conn, "SELECT COUNT(*) as total_users FROM users WHERE role!='admin'");
$total_users = mysqli_fetch_assoc($total_users_result)['total_users'];

$total_sellers_result = mysqli_query($conn, "SELECT COUNT(*) as total_sellers FROM users WHERE role='seller'");
$total_sellers = mysqli_fetch_assoc($total_sellers_result)['total_sellers'];

$total_buyers_result = mysqli_query($conn, "SELECT COUNT(*) as total_buyers FROM users WHERE role='buyer'");
$total_buyers = mysqli_fetch_assoc($total_buyers_result)['total_buyers'];

$total_furniture_result = mysqli_query($conn, "SELECT COUNT(*) as total_furniture FROM furniture WHERE status='active'");
$total_available_furniture = mysqli_fetch_assoc($total_furniture_result)['total_furniture'];

$total_sold_result = mysqli_query($conn, "SELECT COUNT(*) as total_sold FROM furniture WHERE status='sold'");
$total_sold = mysqli_fetch_assoc($total_sold_result)['total_sold'];

$total_furniture_result = mysqli_query($conn, "SELECT COUNT(*) as total_furniture FROM furniture");
$total_furniture = mysqli_fetch_assoc($total_furniture_result)['total_furniture'];

// Fetch sold furniture details with buyer and seller
$sold_furniture_details = mysqli_query($conn, "
    SELECT f.name as furniture_name, f.price, u_b.name as buyer_name, u_s.name as seller_name
    FROM orders o
    JOIN furniture f ON o.furniture_id=f.id
    JOIN users u_b ON o.buyer_id=u_b.id
    JOIN users u_s ON o.seller_id=u_s.id
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($role); ?> Dashboard - Furniture Hub</title>
    <link rel="stylesheet" href="styles.css">
    <style>

    </style>
</head>

<body>

    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($name); ?> 👋</h2>
        <p>You are logged in as <strong><?php echo $email; ?></strong> (<?php echo ucfirst($role); ?>).</p>

        <div class="dashboard-links">
            <?php if ($role === "seller"): ?>
            <a href="furniture_list.php">Manage My Furniture</a>
            <a href="profile.php">Edit Profile</a>
            <?php elseif ($role === "buyer"): ?>
            <a href="furniture_list.php">Browse Furniture</a>
            <a href="profile.php">Edit Profile</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($role === "admin"): ?>
    <div class="dashboard-container">
        <div class="dashboard-links">
            <div>
                <h3>Total Registered Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div>
                <h3>Total Sellers</h3>
                <p><?php echo $total_sellers; ?></p>
            </div>
            <div>
                <h3>Total Buyers</h3>
                <p><?php echo $total_buyers; ?></p>
            </div>
            <div>
                <h3>Total Furniture</h3>
                <p><?php echo $total_furniture; ?></p>
            </div>
            <div>
                <h3>Total Available Furniture</h3>
                <p><?php echo $total_available_furniture; ?></p>
            </div>
            <div>
                <h3>Total Sold Furniture</h3>
                <p><?php echo $total_sold; ?></p>
            </div>
        </div>

        <h3>Sold Furniture Details</h3>
        <table>
            <tr>
                <th>Furniture Name</th>
                <th>Price</th>
                <th>Buyer</th>
                <th>Seller</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($sold_furniture_details)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['furniture_name']); ?></td>
                <td>Pkr <?php echo $row['price']; ?></td>
                <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
            </tr>
            <?php } ?>
        </table>

        <div class="dashboard-links" style="margin-top:2rem;">
            <a href="manage_users.php">Manage Users</a>
            <a href="furniture.php">Manage Furniture</a>
        </div>
    </div>
    <?php endif; ?>

</body>

</html>