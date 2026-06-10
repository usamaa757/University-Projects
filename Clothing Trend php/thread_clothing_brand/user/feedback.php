<?php
include 'header.php';

// Include database connection
include '../db_connection.php';
$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $title = htmlspecialchars($_POST['title']);
    $message = htmlspecialchars($_POST['message']);

    // Prepare SQL query to insert feedback into the database
    $query = "INSERT INTO feedback (user_id, title, text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $title, $message);

    // Execute the query
    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['success'] = "Your feedback has been submitted successfully!";
    } else {
        // Set error message in session
        $_SESSION['error'] = "There was an issue submitting your feedback. Please try again.";
    }

    // Close the prepared statement
    $stmt->close();

    // Redirect back to avoid re-submission
    header('Location: feedback.php');
    exit;
}

// Close the database connection
mysqli_close($conn);
?>

<div class="container mt-5 round shadow border p-3" style="max-width: 600px;">
    <h3>Feedback Form</h3>

    <!-- Display success or error messages from session -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);  // Remove message after displaying it
            ?>
    </div>
    <?php elseif (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);  // Remove message after displaying it
            ?>
    </div>
    <?php endif; ?>

    <!-- Feedback Form -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
        </div>

        <div class="text-center mb-4">
            <button type="submit" class="btn text-white bg-primary">Submit Feedback</button>
        </div>
    </form>
</div>

</body>

</html>