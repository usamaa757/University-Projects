  <!-- Pay with Stripe -->
  <form action="stripe_payment.php" method="post">
      <script src="https://checkout.stripe.com/checkout.js" class="stripe-button" data-key="YOUR_PUBLISHABLE_KEY"
          data-amount="<?= $artwork['price'] * 100; ?>" data - name="Art Gallery" data -
          description="<?= $artwork['title']; ?>" data - image="art.png" data - currency="usd">
      </script>
  </form>
  <?php
    include 'db.php';
    // Check if artwork ID is provided
    if (!isset($_GET['art_id']) || !is_numeric($_GET['art_id'])) {
        die("Invalid artwork ID.");
    }

    $art_id = $_GET['art_id'];

    // Fetch Artwork Details
    $sql = "SELECT a.*, u.username
        FROM art_items a
        JOIN users u ON a.seller_id = u.user_id
        WHERE art_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $art_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Artwork not found.");
    }

    $artwork = $result->fetch_assoc();

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $customer_name = $_POST['name'];
        $customer_email = $_POST['email'];
        $address = $_POST['address'];
        $payment_method = $_POST['payment_method'];

        // Insert order into database
        $order_sql = "INSERT INTO orders (art_id, customer_name, customer_email, address, payment_method) VALUES (?, ?, ?, ?, ?)";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("issss", $art_id, $customer_name, $customer_email, $address, $payment_method);

        if ($order_stmt->execute()) {
            echo "<script>alert('Order placed successfully!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Error placing order. Please try again.');</script>";
        }

        $order_stmt->close();
    }

    ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Checkout - <?php echo htmlspecialchars($artwork['title']); ?></title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>

      <!-- Navigation -->
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
          <div class="container">
              <a class="navbar-brand" href="index.php">Arts Gallery</a>
          </div>
      </nav>

      <!-- Checkout Form -->
      <div class="container mt-5 border rounded shadow p-3">
          <h2 class="text-center">Checkout</h2>
          <div class="row">
              <div class="col-md-6">
                  <img src="<?php echo htmlspecialchars($artwork['image']); ?>" class="img-fluid rounded" alt="Artwork">
              </div>
              <div class="col-md-6">
                  <h3><?php echo htmlspecialchars($artwork['title']); ?></h3>
                  <p class="text-muted">By <?php echo htmlspecialchars($artwork['username']); ?></p>
                  <h4 class="text-success">$<?php echo number_format($artwork['price'], 2); ?></h4>

                  <form method="post" action="payment.php">
                      <div class="mb-3">
                          <label class="form-label">Full Name</label>
                          <input type="text" name="name" class="form-control" required>
                      </div>
                      <div class="mb-3">
                          <label class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" required>
                      </div>
                      <div class="mb-3">
                          <label class="form-label">Shipping Address</label>
                          <textarea name="address" class="form-control" required></textarea>
                      </div>
                      <div class="mb-3">
                          <label class="form-label">Payment Method</label>
                          <select name="payment_method" class="form-select" required>
                              <option value="Online">Online Payment</option>
                              <option value="Cash on Delivery">Cash on Delivery</option>
                          </select>
                      </div>
                      <button type="submit" class="btn btn-success">Place Order</button>
                      <a href="index.php" class="btn btn-secondary">Cancel</a>
                  </form>
              </div>
          </div>
      </div>

      <!-- Footer -->
      <footer class="bg-dark text-white text-center py-3 mt-5">
          <p>&copy; 2025 Arts Gallery. All Rights Reserved.</p>
      </footer>

  </body>

  </html>

  <?php
    // Close Connection
    $conn->close();
    ?>