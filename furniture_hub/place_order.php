<?php
include("config.php");
include("navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = $error = '';

// Get furniture_id and seller_id from GET
if (isset($_GET['furniture_id']) && isset($_GET['seller_id']) && isset($_GET['price'])) {
    $furniture_id = intval($_GET['furniture_id']);
    $seller_id = intval($_GET['seller_id']);
    $total_price = floatval($_GET['price']);

    // Fetch seller info
    $seller_result = mysqli_query($conn, "SELECT account_no, account_detail, name FROM users WHERE id='$seller_id'");
    if (mysqli_num_rows($seller_result) > 0) {
        $seller = mysqli_fetch_assoc($seller_result);
        $seller_name = $seller['name'];
        $seller_account = $seller['account_no'];
        $account_detail = $seller['account_detail'];
    } else {
        $error = "Seller not found!";
    }

    // Fetch furniture details
    $furniture_result = mysqli_query($conn, "SELECT * FROM furniture WHERE id='$furniture_id'");
    if (mysqli_num_rows($furniture_result) > 0) {
        $furniture = mysqli_fetch_assoc($furniture_result);
        if ($furniture['status'] == 'sold') {
            $error = "This furniture is already sold!";
        }
    } else {
        $error = "Furniture not found!";
    }
} else {
    $error = "Invalid request!";
}

// Place order
if (isset($_POST['place_order']) && empty($error)) {
    $buyer_id = $_SESSION['user_id'];
    $furniture_id = intval($_POST['furniture_id']);
    $seller_id = intval($_POST['seller_id']);
    $total_price = floatval($_POST['total_price']);
    $payment_method = $_POST['payment_method'];

    // Check if furniture is already ordered
    $check_order = mysqli_query($conn, "SELECT * FROM orders WHERE furniture_id='$furniture_id'");
    if (mysqli_num_rows($check_order) > 0) {
        $error = "This furniture has already been ordered!";
    } else {
        $sql = "INSERT INTO orders (buyer_id, seller_id, furniture_id, total_price, payment_method)
                VALUES ('$buyer_id','$seller_id','$furniture_id','$total_price','$payment_method')";
        if (mysqli_query($conn, $sql)) {
            mysqli_query($conn, "UPDATE furniture SET status='sold' WHERE id='$furniture_id'");
            $msg = "Order placed successfully! Please follow the payment instructions.";
        } else {
            $error = "Something went wrong. Try again!";
        }
    }
}
?>

<div class="form-container">
    <h2>Place Your Order</h2>

    <?php if (!empty($msg)): ?>
    <div class="success-box"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (empty($error) && isset($furniture)): ?>
    <div class="furniture-details">
        <h3>Furniture Details</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($furniture['name']); ?></p>
        <p><strong>Price:</strong> Pkr <?php echo $furniture['price']; ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($furniture['description']); ?></p>
        <p><strong>Condition:</strong> <?php echo htmlspecialchars($furniture['condition_status']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($furniture['category']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($furniture['location']); ?></p>
        <?php if (!empty($furniture['image'])): ?>
        <p><strong>Image:</strong><br>
            <img src="uploads/<?php echo $furniture['image']; ?>" width="360"
                style="border-radius:5px; object-fit:cover;">
        </p>
        <?php endif; ?>
    </div>

    <h3>Seller Details</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($seller_name); ?></p>
    <p><strong>Account Number:</strong> <?php echo htmlspecialchars($seller_account); ?></p>
    <p><strong>Account Detail:</strong> <?php echo htmlspecialchars($account_detail); ?></p>

    <form method="POST">
        <input type="hidden" name="furniture_id" value="<?php echo $furniture_id; ?>">
        <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>">
        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

        <label>Select Payment Method:</label>
        <select name="payment_method" required>
            <option value="">--Choose Payment--</option>
            <option value="COD">Cash on Delivery</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>

        <button type="submit" name="place_order">Place Order</button>
    </form>
    <?php endif; ?>

    <p><a href="furniture_list.php">Back to Furniture List</a></p>
</div>