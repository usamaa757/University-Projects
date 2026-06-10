<?php
include 'header.php';
include 'db_connect.php';

$agent_id = isset($_SESSION['agent_id']);
$message  = "";
// Get current search filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $conn->query("UPDATE orders SET seed_id = NULL WHERE seed_id = '$delete_id'");

    $conn->query("DELETE FROM seeds WHERE seed_id='$delete_id' AND agent_id='$agent_id'");

    $message = "<p style='color:green;'>Seed deleted successfully. Related orders remain intact.</p>";
}

// Fetch distinct categories for dropdown
$categories = [];
$cat_query = "SELECT DISTINCT category FROM seeds WHERE status = 'Available'";
$cat_result = $conn->query($cat_query);
if ($cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Build main seed query dynamically
$sql = "SELECT * FROM seeds WHERE status = 'Available'";

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $sql .= " AND seed_name LIKE '%$search_safe%'";
}

if (!empty($category)) {
    $category_safe = $conn->real_escape_string($category);
    $sql .= " AND category = '$category_safe'";
}

// Order latest first
$sql .= " ORDER BY upload_date DESC";

$result = $conn->query($sql);
?>
<div class="container">


    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by seed name..."
            value="<?php echo htmlspecialchars($search); ?>">

        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($category == $cat) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat); ?>
            </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">Search</button>
    </form>
</div>
<h2>Seeds List</h2>
<p style="color:green; text-align:center;"><?php echo $message; ?></p>

<div class="seed-list">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
            <div class='seed-card'>
                <img src='uploads/{$row['image']}' alt='{$row['seed_name']}' width='150' height='120'>
                <h3>{$row['seed_name']}</h3>
                <p>Category: {$row['category']}</p>
                <p>Price: Rs. {$row['price_per_kg']} per kg</p>";
            if (isset($_SESSION['user_id'])) {
                echo  "<a href='seed_detail.php?id={$row['seed_id']}' class='btn'>View Details</a>";
            } elseif (isset($_SESSION['agent_id'])) {
                echo "<a href='edit_seed.php?id={$row['seed_id']}'>Edit</a>
                <a href='seeds.php?delete={$row['seed_id']}'>Delete</a>";
            } elseif (!isset($_SESSION['user_id'])) {
                echo "<a href='seed_detail.php?id={$row['seed_id']}' class='btn'>View Details</a>";
            }

            echo "  </div>";
        }
    } else {
        echo "<p>No seeds found matching your search.</p>";
    }
    ?>
</div>

</body>

</html>