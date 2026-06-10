<?php
include 'header.php';
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Insert the new category into the database
    $query = "INSERT INTO categories (category_name, description) VALUES ('$category_name', '$description')";

    if (mysqli_query($conn, $query)) {
        echo "Category added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <form action="add_category.php" method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" id="category_name" name="category_name" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>

        <button type="submit">Add Category</button>
    </form>

</div>