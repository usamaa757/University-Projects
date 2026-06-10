<?php
include '../db.php';
include 'header.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

$art_id = $_GET['art_id'];
$artQuery = $conn->prepare("SELECT * FROM arts WHERE art_id = ?");
$artQuery->bind_param("i", $art_id);
$artQuery->execute();
$art = $artQuery->get_result()->fetch_assoc();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $art_id = $_POST['art_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    $total_amount = $art['price'];
    $order_date = date('Y-m-d H:i:s');

    // Save to session (this is fine for online payment or COD)
    $_SESSION['cart'] = [
        'art_id' => $art_id,
        'art_name' => $art['art_name'],
        'price' => $art['price'],
        'image' => $art['image'],
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'payment_method' => $payment_method,
        'address' => $address,
        'total_amount' => $art['price']
    ];

    // If payment method is Cash on Delivery, insert the order now
    if ($payment_method === "Cash on Delivery") {
        $insert = $conn->prepare("INSERT INTO orders (customer_id, art_id, customer_email, phone, address, payment_method, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $status = "Pending";
        $insert->bind_param("iissssss", $user_id, $art_id, $email, $phone, $address, $payment_method, $order_date, $status);
        $insert->execute();
        $order_id = $conn->insert_id;

        $insert->close();

        // Redirect to confirmation page with order_id
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit;
    } else {
        // For online payment, go to checkout
        header("Location: checkout.php");
        exit;
    }
}

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border rounded shadow p-3">
                <h3 class="mb-4 text-center">Purchase Art: <?= htmlspecialchars($art['art_name']) ?></h3>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <img src="<?= htmlspecialchars($art['image']) ?>" class="img-fluid rounded"
                            alt="<?= htmlspecialchars($art['art_name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <p><strong>Price:</strong> Rs. <?= number_format($art['price'], 2) ?></p>
                        <p><strong>Location:</strong> <?= $user['address'] ?></p>
                        <p><strong>Description:</strong></p>
                        <p><?= nl2br(htmlspecialchars($art['description'] ?? '')) ?></p>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="art_id" value="<?= $art_id ?>">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adress</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Cash on Delivery">Cash on Delivery</option>
                            <option value="Online">Online Payment</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>