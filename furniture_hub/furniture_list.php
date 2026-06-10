<?php
include 'config.php';
include 'navbar.php';

// Base query
$query = "SELECT * FROM furniture WHERE status != 'block'";

// Delete furniture
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM furniture WHERE id=$id");

    $msg = "Furniture deleted successfully!";
}

// Filters
$filters = [];

if (!empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $filters[] = "(name LIKE '%$keyword%' OR description LIKE '%$keyword%')";
}

if (!empty($_GET['min_price'])) {
    $min_price = floatval($_GET['min_price']);
    $filters[] = "price >= $min_price";
}

if (!empty($_GET['max_price'])) {
    $max_price = floatval($_GET['max_price']);
    $filters[] = "price <= $max_price";
}

if (!empty($_GET['name'])) {
    $name = mysqli_real_escape_string($conn, $_GET['name']);
    $filters[] = "name='$name'";
}

if (!empty($_GET['condition_status'])) {
    $condition = mysqli_real_escape_string($conn, $_GET['condition_status']);
    $filters[] = "condition_status='$condition'";
}

if (!empty($_GET['location'])) {
    $location = mysqli_real_escape_string($conn, $_GET['location']);
    $filters[] = "location LIKE '%$location%'";
}

// Append filters to query
if (count($filters) > 0) {
    $query .= " AND " . implode(" AND ", $filters);
}

// Execute query
$result = mysqli_query($conn, $query);

// Fetch categories dynamically
$name_result = mysqli_query($conn, "SELECT DISTINCT name FROM furniture WHERE status != 'block'");
$categories = [];
while ($cat = mysqli_fetch_assoc($name_result)) {
    $categories[] = $cat['name'];
}


?>

<div class="container">
    <h3>Search Furniture</h3>
    <form method="GET" class="search-contiainer">
        <input type="text" name="keyword" placeholder="Keyword (name, description)"
            value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">

        <input type="number" name="min_price" placeholder="Min Price"
            value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
        <input type="number" name="max_price" placeholder="Max Price"
            value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">

        <!-- Category Selection -->
        <select name="category">
            <option value="">--Select Category--</option>
            <option value="Sofa" <?php if (isset($_GET['category']) && $_GET['category'] == 'Sofa') echo 'selected'; ?>>
                Sofa</option>
            <option value="Chair"
                <?php if (isset($_GET['category']) && $_GET['category'] == 'Chair') echo 'selected'; ?>>Chair</option>
            <option value="Table"
                <?php if (isset($_GET['category']) && $_GET['category'] == 'Table') echo 'selected'; ?>>Table</option>
            <option value="Bed" <?php if (isset($_GET['category']) && $_GET['category'] == 'Bed') echo 'selected'; ?>>
                Bed</option>
            <option value="Cabinet"
                <?php if (isset($_GET['category']) && $_GET['category'] == 'Cabinet') echo 'selected'; ?>>Cabinet
            </option>
            <option value="Dresser"
                <?php if (isset($_GET['category']) && $_GET['category'] == 'Dresser') echo 'selected'; ?>>Dresser
            </option>
        </select>


        <select name="condition_status">
            <option value="">--Select Condition--</option>
            <option value="New"
                <?php if (isset($_GET['condition_status']) && $_GET['condition_status'] == 'New') echo 'selected'; ?>>
                New</option>
            <option value="Used"
                <?php if (isset($_GET['condition_status']) && $_GET['condition_status'] == 'Used') echo 'selected'; ?>>
                Used</option>
        </select>

        <input type="text" name="location" placeholder="Location"
            value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">

        <button type="submit" name="search">Search</button>
    </form>

    <h3>Furniture Results</h3>
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Description</th>
            <th>Condition</th>
            <th>Location</th>
            <th>Status</th>
            <?php
            if (isset($_SESSION['user_id'])) { ?>

                <th>Action</th>
            <?php   } ?>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $img = $row['image'];
            echo "<tr>
            <td><img src='uploads/$img' width='80' height='80' style='border-radius:5px; object-fit:cover;'></td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['category']) . "</td>
            <td>Pkr " . htmlspecialchars($row['price']) . "</td>
            <td>" . htmlspecialchars($row['description']) . "</td>
            <td>" . htmlspecialchars($row['condition_status']) . "</td>
            <td>" . htmlspecialchars($row['location']) . "</td>
            <td>" . htmlspecialchars($row['status']) . "</td>";
            if (isset($_SESSION['user_id'])) {

                echo "<td class='action-links'>";

                // Actions based on role
                if ($_SESSION['role'] == 'seller' && $row['seller_id'] == $_SESSION['user_id']) {
                    if ($row['status'] != 'sold'):
                        echo "<a href='furniture_edit.php?id={$row['id']}'>Edit</a>";
                        echo "<a href='furniture_list.php?delete={$row['id']}' onclick=\"return confirm('Delete this item?');\">Delete</a>";
                    else:
                        echo "<strong>N/A</strong>";
                    endif;
                } elseif ($_SESSION['role'] != 'seller') {
                    echo $row['status'] == 'sold' ? "<strong>Sold</strong>" : "<a href='place_order.php?furniture_id={$row['id']}&seller_id={$row['seller_id']}&price={$row['price']}'>Buy Now</a>";
                }

                echo "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>

</div>