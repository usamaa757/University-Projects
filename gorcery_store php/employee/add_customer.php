<?php
include('header.php');
include('../db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subscription_status = $_POST['subscription_status'];

    // Insert customer data into the database
    $query = "INSERT INTO customers (name, email, phone, subscription_status) 
              VALUES ('$name', '$email', '$phone', '$subscription_status')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Customer added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding customer.');</script>";
    }
}
?>

<!-- Add Customer Form -->
<div class="container mt-5 rounded shadow border p-0" style="max-width: 500px;">
    <h3 class="bg-dark text-center text-white p-2">Add New Customer</h3>
    <div class="p-4">
        <form method="POST" action="add_customer.php">
            <div class="form-group">
                <label for="name">Customer Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Customer Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="subscription_status">Subscription Status</label>
                <select class="form-control" id="subscription_status" name="subscription_status" required>
                    <option value="subscribed">Subscribed</option>
                    <option value="unsubscribed">Unsubscribed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Customer</button>
        </form>
    </div>
</div>
</body>

</html>