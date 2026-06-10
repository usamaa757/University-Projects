<?php
include 'header.php';
require '../db.php';



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['group_name']);
    $description = trim($_POST['description']);
    $type = $_POST['type'];
    $created_by = $_SESSION['admin_id'];

    if (!empty($name) && !empty($type)) {
        $query = "INSERT INTO groups (name, description, type, created_by) 
                  VALUES ('$name', '$description', '$type', $created_by)";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Group created successfully.'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error creating group.');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }

    mysqli_close($conn);
}
?>


<div class="form-container">
    <h2>Create New Travel Group</h2>
    <form method="POST" class="forms">
        <label>Group Name:</label>
        <input type="text" name="group_name" required>

        <label>Description:</label>
        <textarea name="description" rows="4"></textarea>

        <label>Group Type:</label>
        <select name="type" required>
            <option value="public">Public</option>
            <option value="private">Private</option>
        </select>


        <div class="text-center">
            <button type="submit" class="btn">Create</button>
        </div>
    </form>
</div>

</body>

</html>