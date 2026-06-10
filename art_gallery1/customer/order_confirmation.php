<?php
include '../db.php';
include 'header.php';

$order_id = $_GET['order_id'];

$order = $conn->query("SELECT o.*, a.art_name, a.price, a.image, a.artist_id,  u.name AS artist_name 
    FROM orders o
    JOIN arts a ON o.art_id = a.art_id
    JOIN users u ON a.artist_id = u.user_id
    WHERE o.order_id = $order_id")->fetch_assoc();

if (!$order) {
    echo "<div class='container mt-5 alert alert-danger'>Order not found.</div>";
    exit;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border rounded p-3">

                <h3 class="mb-0 text-center">Order Confirmation</h3>

                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="../uploads/<?= htmlspecialchars($order['image']) ?>" alt="Art Image"
                            class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Order ID:</span>
                            <span><?= $order['order_id'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Art Name:</span>
                            <span><?= htmlspecialchars($order['art_name']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Artist:</span>
                            <span><?= htmlspecialchars($order['artist_name']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Total Price:</span>
                            <span>Rs. <?= number_format($order['price'], 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Payment Method:</span>
                            <span><?= $order['payment_method'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Payment Status:</span>
                            <span class="text-capitalize"><?= $order['status'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Order Date:</span>
                            <span><?= date('d M, Y', strtotime($order['order_date'])) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Shipping Status:</span>
                            <span class="text-capitalize"><?= $order['shipping_status'] ?></span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>