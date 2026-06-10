<?php

include 'header.php';
include '../db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $_POST['service_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];

    $query = "INSERT INTO services (service_name, category, price, description, duration)
              VALUES ('$service_name', '$category', '$price', '$description', '$duration')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Service added successfully!'); window.location.href='service.php';</script>";
    } else {
        echo "<script>alert('Error adding service');</script>";
    }
}
?>


<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Add New Product</h2>
    </div>


    <form action="add_service.php" method="POST" class="form">
        <label>Service Name:</label>
        <input type="text" name="service_name" placeholder="Add Name" required><br><br>

        <label>Category:</label>
        <input type="text" name="category" placeholder="Hair, Skincare, Makeup..."><br><br>

        <label>Description:</label>
        <textarea name="description" rows="4" cols="67" placeholder="Description"></textarea><br><br>

        <label>Price ($):</label>
        <input type="number" name="price" step="0.01" required placeholder="Price"><br><br>

        <label>Duration:</label>
        <input type="text" name="duration" placeholder="e.g., 30 mins"><br><br>


        <div class="btn-div">
            <button type="submit" class="btn" value="Add Product">Add</button>

        </div>
    </form>
</div>
</body>

</html>