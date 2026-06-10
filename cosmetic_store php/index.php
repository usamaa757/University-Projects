<?php
include 'header.php';
?>

<!-- Banner Section -->
<section class="banner">
    <div class="overlay"></div>
    <div class="banner-content">
        <h1>Enhance Your Beauty</h1>
        <p>Premium cosmetics designed for every skin type.</p>
        <a href="#" class="btn-shop">Shop Now</a>
    </div>
</section>
<style>
    .products {
        padding: 40px;
        text-align: center;
    }

    .product-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .product-card {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 10px;
        width: 250px;
        text-align: center;
    }

    .product-card img {
        max-width: 100%;
        height: auto;
    }

    .btn-buy {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 12px;
        background-color: #e91e63;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }
</style>
<?php
include 'db.php';
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!-- Products Section -->
<section class="products">
    <h2>Our Products</h2>
    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                $imagePath = str_replace('../', '', $row['image_path']);
                echo '<img src="' . $imagePath . '" alt="' . $row['product_name'] . '">';
                echo '<h3>' . $row['product_name'] . '</h3>';
                echo '<p>' . $row['category'] . '</p>';
                echo '<p>' . $row['brand'] . '</p>';
                echo '<span>$' . $row['price'] . '</span>';
                echo '</div>';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
    </div>
</section>

<?php $conn->close();

include 'footer.php';
?>


</body>

</html>