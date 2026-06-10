<?php
include 'header.php';
include 'db_connect.php';

// If agent not logged in, redirect to login page
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if (isset($_POST['upload'])) {
    // Get form inputs
    $seed_name = $_POST['seed_name'];
    $category = $_POST['category'];
    $variety = $_POST['variety'];
    $description = $_POST['description'];
    $price = $_POST['price_per_kg'];
    $quantity = $_POST['quantity_available'];
    $agent_id = $_SESSION['agent_id'];
    $status = "Available";

    // Handle image upload
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // Insert record into database
    $sql = "INSERT INTO seeds (seed_name, category, variety, description, price_per_kg, quantity_available, image, agent_id, status)
            VALUES ('$seed_name', '$category', '$variety', '$description', '$price', '$quantity', '$image', '$agent_id', '$status')";

    if ($conn->query($sql) === TRUE) {
        $message = "Seed uploaded successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>


<div class="container">

    <h3>Upload Seed Details</h3>
    <p style="color:green;"><?php echo $message; ?></p>

    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <label>Seed Name:</label>
        <input type="text" name="seed_name" required>

        <label>Category:</label>
        <input type="text" name="category" placeholder="e.g. Wheat, Rice, Maize" required>

        <label>Variety:</label>
        <input type="text" name="variety" placeholder="e.g. Basmati 515, Inqlab-91">

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Price (PKR per kg):</label>
        <input type="number" name="price_per_kg" min="1" required>

        <label>Quantity Available (kg):</label>
        <input type="number" name="quantity_available" min="0" required>

        <label>Upload Image:</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" name="upload" class="btn">Upload Seed</button>
    </form>

</div>

</body>

</html>