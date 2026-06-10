<?php
include 'header.php';
include '../db_connection.php';

// Fetch plants from the database
$plants_query = "SELECT plant_id, plant_name FROM plants";
$plants_result = $conn->query($plants_query);

// Handle plant query submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $plant_id = $_POST['plant_id'];
    $query_title = $_POST['query_title'];
    $query_description = $_POST['query_description'];

    // Validate the form fields
    if (empty($plant_id) || empty($query_title) || empty($query_description)) {
        $_SESSION['error'] = "Please fill out all fields.";
    } else {
        // Insert the query into the database
        $insert_query = "INSERT INTO plant_queries (user_id, plant_id, query_title, query_description, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiss", $user_id, $plant_id, $query_title, $query_description);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Your plant query has been submitted successfully!";
        } else {
            $_SESSION['error'] = "There was an error submitting your query. Please try again.";
        }
        header("Location: add_query.php");
        exit();
    }
}
?>

<div class="container mt-4 round shadow border" style="max-width: 600px;">
    <div class="text-center p-3">
        <h3>Submit a Plant Query</h3>
    </div>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php elseif (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="add_query.php" method="POST">
        <!-- Select Plant -->
        <div class="form-group">
            <label for="plant_id">Select Plant</label>
            <select class="form-control" id="plant_id" name="plant_id" required>
                <option value="" disabled selected>Select a plant</option>
                <?php while ($plant = $plants_result->fetch_assoc()): ?>
                    <option value="<?php echo $plant['plant_id']; ?>">
                        <?php echo htmlspecialchars($plant['plant_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Query Title -->
        <div class="form-group">
            <label for="query_title">Query Title</label>
            <input type="text" class="form-control" id="query_title" name="query_title" required>
        </div>

        <!-- Query Description -->
        <div class="form-group">
            <label for="query_description">Query Description</label>
            <textarea class="form-control" id="query_description" name="query_description" rows="4" required></textarea>
        </div>

        <div class="text-center mb-3">
            <button type="submit" class="btn bg-primary text-white">Submit Query</button>
        </div>
    </form>
</div>

</body>

</html>