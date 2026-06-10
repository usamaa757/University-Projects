<?php
// Include header
include 'header.php';

// Include database connection
include '../db_connection.php';

// Fetch plants and their respective seller data
$plants_query = "
    SELECT p.*, s.seller_name, s.email 
    FROM plants p 
    JOIN sellers s ON p.seller_id = s.seller_id
";
$plants_result = mysqli_query($conn, $plants_query);
?>

<div class="container mt-5 round border shadow p-3">
    <h3>Plants List</h3>

    <!-- Plants display -->
    <div class="row">
        <?php
        if (mysqli_num_rows($plants_result) > 0) {
            // Loop through plants and display them in a 4-item grid
            while ($plant = mysqli_fetch_assoc($plants_result)) {
                echo "<div class='col-md-3 mb-4 round'>";
                echo "<div class='card mt-3'>";
                echo "<img src='../seller/" . htmlspecialchars($plant['image_url']) . "' class='card-img-top' alt='Plant Image' style='height: 200px; object-fit: cover;'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($plant['plant_name']) . "</h5>";
                echo "<p class='card-text'>Type: " . htmlspecialchars($plant['plant_type']) . "</p>";
                echo "<p class='card-text'>Price: Rs " . htmlspecialchars($plant['price']) . "</p>";
                if ($plant['quantity'] == 0) {
                    echo "<p class='card-text text-red'>Out of stock</p>";
                    // Feedback Button
                    echo "<a href='feedback.php?plant_id=" . htmlspecialchars($plant['plant_id']) . "' class='btn btn-primary btn-sm mt-2'>Feedback</a>
                     <a href='plant_care.php?plant_id=" . htmlspecialchars($plant['plant_id']) . "' class='btn btn-danger btn-sm mt-2'>Tips & Care</a>";
                } else {

                    echo "<p class='card-text'>Stock: " . htmlspecialchars($plant['quantity']) . "</p>";


                    // Display seller info
                    echo "<p class='card-text'><small>Seller: " . htmlspecialchars($plant['seller_name']) . "</small></p>";

                    // Add to Cart Form
                    echo "<form action='cart.php' method='POST'>";
                    echo "<div class='form-group'>";
                    echo "<label for='quantity'>Select Quantity</label>";
                    echo "<input type='number' name='quantity' min='1' max='" . htmlspecialchars($plant['quantity']) . "' class='form-control' required>";
                    echo "<input type='hidden' name='plant_id' value='" . htmlspecialchars($plant['plant_id']) . "'>";
                    echo "</div>";
                    echo "<button type='submit' class='btn btn-success btn-sm'>Add to Cart</button>";
                    echo "</form>";

                    // Feedback Button
                    echo "<a href='feedback.php?plant_id=" . htmlspecialchars($plant['plant_id']) . "' class='btn btn-primary btn-sm mt-2'>Plant Review</a>
                    <a href='plant_care.php?plant_id=" . htmlspecialchars($plant['plant_id']) . "' class='btn btn-danger btn-sm mt-2'>Tips & Care</a>
                    <a href='seller_reviews.php?seller_id=" . htmlspecialchars($plant['seller_id']) . "' class='btn btn-primary btn-sm mt-2'>Seller Review</a>";
                }
                echo "</div>"; // Close card-body
                echo "</div>"; // Close card
                echo "</div>"; // Close col-md-3
            }
        } else {
            echo "<p>No plants listed yet.</p>";
        }

        // Close the connection
        mysqli_close($conn);
        ?>
    </div> <!-- Close row -->
</div> <!-- Close container -->

</body>

</html>