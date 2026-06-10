<?php
include 'sidebar.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $test_name = $_POST['test_name'];


    $stmt = $conn->prepare("INSERT INTO tests (test_name) VALUES (?)");
    $stmt->bind_param("s", $test_name);
    if ($stmt->execute()) {

        echo "<script>alert('Test added successfully'); window.location='test_list.php';</script>";
    } else {
        echo "<script>alert('Failed to add test.'); window.history.back();</script>";
        exit;
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="container p-3 card">
        <h4 class="mb-3 text-center">Add Test</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Test Name</label>
                <input type="text" name="test_name" class="form-control" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="test_list.php" class="btn btn-secondary">Back to List</a>
            </div>
        </form>
    </div>
</div>