<?php
ob_start(); // Start output buffering
include 'header.php';
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);

    // Insert into students table
    $sql = "INSERT INTO subjects (subject_name) VALUES ('$subject_name')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Subject added!";
        header("Location: add_subject.php?status=success&success=" . urlencode($_SESSION['success']));
    } else {
        $_SESSION['error'] = "Error: Could not add.";
        header("Location: add_subject.php?status=error&error=" . urlencode($_SESSION['error']));
    }
    exit();
}

?>
<br><br><br><br><br>
<div class="container border rounded shadow p-0 w-25">
    <div class="bg-primary text-center">
        <h3 class="text-center text-white p-2">Add New Subject</h3>
    </div>
    <div class="p-2">

        <a href="subjects.php" class="btn btn-block btn-primary">Subject List</a>
    </div>
    <div class="p-3">
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="text text-success" role="alert">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']); // Clear message after displaying
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="text text-danger" role="alert">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Clear message after displaying
        }
        ?>
    </div>

    <form method="POST" class="p-4">
        <div class="form-group mb-3">
            <label for="subject_name" class="form-label">Subject Name:</label>
            <input type="text" name="subject_name" id="subject_name" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block">Add Subject</button>
        </div>
    </form>
</div>

<?php

ob_end_flush(); // Flush the output buffer and turn off output buffering
?>