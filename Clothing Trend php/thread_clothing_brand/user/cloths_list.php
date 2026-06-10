<?php
// Include header
include 'header.php';

// Include database connection
include '../db_connection.php';

// Fetch cloths with category names
$cloths_query = "
    SELECT 
        c.cloth_id, 
        c.size, 
        c.price, 
        c.quantity, 
        c.image_url, 
        cat.category_name 
    FROM 
        cloths c
    LEFT JOIN 
        categories cat 
    ON 
        c.category_id = cat.category_id
";

// Execute query
$cloths_result = mysqli_query($conn, $cloths_query);
?>
<div class="container mt-5 round border shadow p-3">
    <h3>Cloths List</h3>

    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" id="searchBar" class="form-control" placeholder="Search cloths by name or type..."
            onkeyup="searchCloths()">
    </div>

    <!-- cloths display -->
    <div class="row" id="clothsContainer">
        <?php
        if (mysqli_num_rows($cloths_result) > 0) {
            // Loop through cloths and display them in a 4-item grid
            while ($cloth = mysqli_fetch_assoc($cloths_result)) {
                echo "<div class='col-md-3 mb-4 round cloth-item'>";
                echo "<div class='card mt-3'>";
                echo "<img src='$base_url/admin/" . htmlspecialchars($cloth['image_url']) . "' class='card-img-top' alt='cloth Image' style='height: 200px; object-fit: cover;'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-text'>Type: " . htmlspecialchars($cloth['category_name']) . "</h5>";
                echo "<p class='card-text'>Price: Rs " . htmlspecialchars($cloth['price']) . "</p>";
                if ($cloth['quantity'] == 0) {
                    echo "<p class='card-text text-red'>Out of stock</p>";
                } else {
                    echo "<p class='card-text'>Stock: " . htmlspecialchars($cloth['quantity']) . "</p>";
                    echo "<form action='cart.php' method='POST'>";
                    echo "<div class='form-group'>";
                    echo "<label for='quantity'>Select Quantity</label>";
                    echo "<input type='number' name='quantity' min='1' max='" . htmlspecialchars($cloth['quantity']) . "' class='form-control' required>";
                    echo "<input type='hidden' name='cloth_id' value='" . htmlspecialchars($cloth['cloth_id']) . "'>";
                    echo "</div>";
                    echo "<button type='submit' class='btn btn-success btn-sm'>Add to Cart</button>";
                    echo "</form>";
                }
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No cloths listed yet.</p>";
        }

        // Close the connection
        mysqli_close($conn);
        ?>
    </div> <!-- Close row -->
</div> <!-- Close container -->
<script>
function searchCloths() {
    const searchQuery = document.getElementById('searchBar').value.toLowerCase().trim();
    const clothItems = document.querySelectorAll('.cloth-item');

    clothItems.forEach((item) => {
        const clothName = item.querySelector('.card-title').textContent.toLowerCase();
        const clothType = item.querySelector('.card-text').textContent.toLowerCase();
        const clothPrice = item.querySelector('.card-text:nth-of-type(2)').textContent.toLowerCase(); // Price
        const clothStock = item.querySelector('.card-text:nth-of-type(3)') ?
            item.querySelector('.card-text:nth-of-type(3)').textContent.toLowerCase() :
            ""; // Stock or Out of Stock

        const combinedFields = `${clothName} ${clothType} ${clothPrice} ${clothStock}`;

        if (combinedFields.includes(searchQuery)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>